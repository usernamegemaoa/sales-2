<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR."/modules/sales/quotation.js.php");
require_once(DP_BASE_DIR . '/modules/departments/CDepartment.php');
require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
$CTemplatePDF = new CTemplatePDF();
            
            // active default Template pdf //
$template_pdf_1 = $CTemplatePDF->get_template_pdf(1);
$template_server = $template_pdf_1[0]['template_server'];
$CCompanyManager = new CompanyManager();
$CDepManager = new CDepartment();

// Checking permissions edit
$perms =& $AppUI->acl();
$canEdit = $perms->checkModule( $m, 'edit');

$addView_quotation = true;
//$canView_quotation = $perms->checkModule( 'sales_quotation', 'view');
$addView_quotation = $perms->checkModule( 'sales_quotation', 'add');
$editView_quotation = $perms->checkModule( 'sales_quotation', 'edit');
$deleteView_quotation = $perms->checkModule( 'sales_quotation', 'delete');



define(FORMAT_DATE_DEFAULT, 'd/m/Y');

$isUserDepartment = $CDepManager->IsUserInDepartment();

$status_id = 0;
if(isset($_GET['s']))
    $status_id = $_GET['s'];

echo '<div id="div_quotation">';
    //echo $new_quotation_block = $AppUI->createBlock('button_new', '<button class="ui-button ui-state-default ui-corner-all" onclick="load_quotation(0, 0, \'add\')"><img border="0" src="images/icons/plus.png">New Quotation</button>&nbsp;&nbsp;', 'style="text-align: center; float: left; margin-right : 10 px"');
    
    if($addView_quotation)
        $btn_new = '<button class="ui-button ui-state-default ui-corner-all" onclick="load_quotation(0, 0, \'add\')"><img border="0" src="images/icons/plus.png">New Quotation</button>&nbsp;&nbsp;';
    //echo $button_block = $AppUI->createBlock('button_block', $new_block . $print_block . $email_block, 'style="text-align: center; float: left"');
    //echo '<br><br>';

    $quotation_stt = array('5' => 'All');
    $quotation_stt += dPgetSysVal('QuotationStatus');
    if($template_server == 4)
        $quotation_stt_dropdown = "Status: ".arraySelect($quotation_stt, 'quotation_status_id', 'id="quotation_status_id" class="text" size="1"',5, true)."&nbsp;&nbsp;&nbsp;";
    else
        $quotation_stt_dropdown = "Status: ".arraySelect($quotation_stt, 'quotation_status_id', 'id="quotation_status_id" class="text" size="1"',$status_id, true)."&nbsp;&nbsp;&nbsp;";
    /*====================================================/*
        /* display the select list DEPARMENT*/ 
     /*===================================================*/
    $buffer = 'Department: <select id="department" name="department" class="text">';
    $buffer .= '<option value="" style="font-weight:bold;">'.$AppUI->_('Select').'</option>'."\n";
    $company = '';
    
    $dep_user_id=0;
    $dep_user_arr = $CDepManager->getDeparmentByUser($AppUI->user_id);
    $dep_user_id_arr = array();
    if(count($dep_user_arr)>0)
    {
        //$dep_user_id = $dep_user_arr[0]['dept_id'];
        foreach ($dep_user_arr as $dep_user_row) {
            $dep_user_id_arr[] = $dep_user_row['dept_id'];
            $dep_user_id = $dep_user_arr[0]['dept_id'];
        }
    }
    $select_dep_id = false;
    if(isset($_GET['d']))
    {
        $dep_user_id = $_GET['d'];
        $select_dep_id = $_GET['d'];
    }

    
    $rows = $CCompanyManager->getCompanyJoinDepartment();
    $company ="";
    $company_prefix = 'company_';
    foreach ($rows as $row) {
        if ($row['dept_parent'] == 0) {
            if ($company!=$row['company_id']) {
                    $buffer .= '<option value="' . $company_prefix.$row['company_id'] . '" style="font-weight:bold;">'. $row['company_name'] . '</option>' . "\n";
                    $company=$row['company_id'];
            }
            if ($row['dept_parent']!=null){
                $buffer.=$CDepManager->showchilddept($row,$level,$dep_user_id_arr,$select_dep_id);
                $buffer.=$CDepManager->findchilddept($rows, $row['dept_id'],$level,$dep_user_id_arr,$select_dep_id);
            }
        }
    }
    $buffer .= '</select>'.'&nbsp;&nbsp;&nbsp;';
    /*====================================================/*
        /* END display the select list DEPARMENT*/ 
     /*===================================================*/
    
    $search_quotation_no = 'Quotation No: <input class="text" type="text" name="search-quotation-no" size="12" id="search-quotation-no" value="" />'."&nbsp;&nbsp;&nbsp;";
    
    $button_search = '<button class="ui-button ui-state-default ui-corner-all" onclick="search_quotation();return false;">Search</button>';
    echo $stt_quotation_block = $AppUI->createBlock('stt-block',$btn_new .$search_quotation_no. $quotation_stt_dropdown .'&nbsp;&nbsp;'.$buffer.$button_search, 'style="margin-right : 10 px;padding-bottom:10px;"');
    
    echo '<div id="div-list-quotation">';
    echo '</div></div>';
    //echo '<div id="div_task"><div id="sales_task_popup-editTask" style="display: none;"></div></div>';
?>
<script>
        $(document).ready(function() {
                var status = '<?php echo $_GET['status']; ?>';
                var quotation_id = '<?php echo $_GET['quotation']; ?>';
                var quotation_revision_id = '<?php echo $_GET['quotation_revision_id']; ?>';
                var s = <?php echo $status_id; ?>;
                var dept_id = '<?php echo $dep_user_id; ?>';
                var b = '<?php echo $_GET['s']; ?>';
                if(b!="")
                    list_quotation('',s,status,quotation_id,quotation_revision_id,dept_id,'');
                if(status!="" && quotation_id!="" && quotation_revision_id!="")
                    load_quotation(quotation_id,quotation_revision_id,status);
	});
</script>