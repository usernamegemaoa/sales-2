<?php

if(!defined("DP_BASE_DIR")){
    die('You should not access this file directly.');
}
require_once(DP_BASE_DIR. '/modules/system/cconfig.class.php');

require_once (DP_BASE_DIR."/modules/sales/CCreditNoteManager.php");
require_once(DP_BASE_DIR."/modules/clients/company_manager.class.php");
require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
$CCreditManager = new CCreditNoteManager();
$CCompany = new CompanyManager();
$InvoiceManager = new CInvoiceManager();
$CSalesManager = new CSalesManager();

$perms =& $AppUI->acl();

$canAdd = $perms->checkModule($m, 'add');
$canDelete = $perms->checkModule($m, 'delete');
$canEdit = $perms->checkModule($m, 'edit');
$canRead = $perms->checkModule($m, 'view');

$canView_creditNote = $perms->checkModule( 'sales_creditNote', 'view');
$addView_creditNote = $perms->checkModule( 'sales_creditNote', 'add');
$editView_creditNote = $perms->checkModule( 'sales_creditNote', 'edit');
$deleteView_creditNote = $perms->checkModule( 'sales_creditNote', 'delete');
$accessView_creditNote = $perms->checkModule( 'sales_creditNote', 'access');

function list_creditNote(){
    $objConfig = new CConfigNew();
    global $ocio_config,$AppUI,$CCreditManager,$CSalesManager,$deleteView_creditNote,$canDelete, $accessView_creditNote;
    $invoice_stt = dPgetSysVal('InvoiceStatus');
    $status = $_POST['status'];
    $credit_status =  dPgetSysVal('CreditStatus');
    $dataTableRow = new JDataTable('creditNote_table');
    $dataTableRow ->setWidth('100%');
    $dataTableRow ->setHeaders(array('<input type="checkbox" onclick="check_all(\'select_all\', \'check_list\');" name="select_all" id="select_all" />','Credit Note','Customer','Date','Invoice No','Invoice Status','Total Amount','Status'));
    $table = '<table id="contracts_table" class="tbl" cellspacing="1" cellpadding="2" style="clear: both; width: 100%">
        <thead>
            <tr>
                <th class="checkboc" width="5%" align="center"></th>
                <th class="credit no" width="10%">Credit Note</th>
                <th class="customer_credit" width="40%">Customer<br>(Location)</th>
                <th class="date_credit" width="12%" align="center">Date</th>
                <th class="invocie_no" width="12%" align="left">Invoice No</th>
                <th class="invocie_status" width="12%" align="center" style="width: 47px;">Invoice Status</th>
                <th class="amount_credit" width="15%" align="right">Total Amount</th>
                <th calss="status_credit" width="11%" align="center">Status</th>
         
            </tr>
        </thead>
    </table>';
    echo $table;
    ?>
<script type="text/javascript"> 
       $(document).ready(function() {
           
            
           
            
            $('#contracts_table').dataTable({
                    'sprocessing':true,
                    'bServerSide':true,
                    "bDestroy": true,
                    "border": [[ 0, "desc" ]],
                    'sAjaxSource':'?m=sales&a=_ajax_creditNote&suppressHeaders=true&status=<?php echo $_POST[status];?>',
                    'aoColumns':[
                        {"align": "left","bSortable": false},
                        {"align": "left","asSorting": [ "desc" ]},
                        {"align": "left"},
                        {"align": "left","bSortable": false},
                        {"align": "left","bSortable": false},
                        {"align": "left","sClass": "text_center","bSortable": false},
                        {"align": "left","bSortable": false},
                        {"align": "left","sClass": "text_right","bSortable": false},
//                        {"align": "left","sClass": "text_right","bSortable": false},
//                        {"align": "left","sClass": "text_right","bSortable": false},
//                        {"align": "left","sClass": "text_right","bSortable": false},
                    ],
            });
        });
</script>
<?php
    
//    $colAttributer = array(
//        'class="checkboc" width="5%" align="center"',
//        'class="credit no" width="10%"',
//        'class="customer_credit" width="40%"',
//        'class="date_credit" width="12%" align="center"',
//        'class="invocie_no" width="12%" align="left"',
//        'class="invocie_status" width="12%" align="center"',
//        'class="amount_credit" width="15%" align="right"',
//        'calss="status_credit" width="11%" align="center"'
//    );
//    $dataTable=array(); $rowIds=array();
//    $creditNote_arr = $CCreditManager->list_db_creditNote(false,false,$status);
////    print_r($creditNote_arr);
//    foreach ($creditNote_arr as $creditNote_row){
//        $status = $creditNote_row['credit_note_status'];
//        $customer_err = $CCreditManager->customer_by_creditNote($creditNote_row['customer_id']);
//        $invoice_id = $creditNote_row['invoice_id'];
//        $invoice_manage = new CInvoiceManager();
//        $invocie = $invoice_manage->get_db_invoice($invoice_id);
//        $invoice_revision_id = $invoice_manage->get_invoice_revision_lastest($invoice_id);
//        $invoice_no = $invocie[0]['invoice_no'];
////        print_r($customer_err);
//        $dataTable[] = array(
//            '<input type="checkbox" name="check_list" id="check_list" value="'.$creditNote_row['credit_note_id'].'">',
//            '<a href="" onclick="load_creditNote('.$creditNote_row['credit_note_id'].',\'update\'); return false;">'.$creditNote_row['credit_note_no'].'</a>',
//            $customer_err[0]['company_name'],
//            date('d/m/Y', strtotime($creditNote_row['credit_note_date'])),
//            '<a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_revision_id.'">'.$invoice_no.'</a>',
//            $invoice_stt[$invocie[0]['invoice_status']],
//            '$'.number_format($CSalesManager->total_creditNote_amount_and_tax($creditNote_row['credit_note_id']),2),
//            $credit_status[$status]
//        );
//        $rowIds[] = $creditNote_row['credit_note_id'];
//    }
//    $dataTableRow->setDataRow($dataTable, $colAttributer, $rowIds);
//    $dataTableRow->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
//    $dataTableRow->show();
    if($deleteView_creditNote && $canDelete)
        echo '<a class="icon-delete icon-all" onclick="delete_creditNote(); return false;" href="#">Delete</a>';
}
function view_creditNote_detail(){
    global $AppUI,$CCreditManager;
    require_once (DP_BASE_DIR."/modules/sales/creditNote.js.php");
    if(isset($_REQUEST['creditNote_id'])){
        $creditNote_id = $_REQUEST['creditNote_id'];
        $creditNote_arr=$CCreditManager->list_db_creditNote($creditNote_id);
        $creditNote_title="Credit Note: ".$creditNote_arr[0]['credit_note_no'];
    }
    else
        $creditNote_title = "New Credit Note";
    if(isset($_REQUEST['status']))
        $status = $_RE['status'];
    echo '<div id="creditNote_title">'.$creditNote_title.'</div>';
    echo "<div id='form_button'>";
        form_button($creditNote_id);
    echo "</div>";
    echo "<div id='form_info'>";
        form_info($creditNote_id);
    echo "</div>";
    echo "<div id='form_item'>";
        form_table_item($creditNote_id);
    echo "</div>";
}
function form_button($creditNote_id=""){
    global $AppUI,$CCreditManager,$CSalesManager,$editView_creditNote,$canEdit,$canAdd,$addView_creditNote;
    if($creditNote_id!=""){
        $creditNote_arr = $CCreditManager->list_db_creditNote ($creditNote_id);
        $total_amount = $CSalesManager->total_creditNote_amount_and_tax($creditNote_id);
        if($creditNote_arr[0]['credit_note_status']!=2 && $editView_creditNote && $canEdit)
            $btn_save = '<button id="creditnote_update" class="ui-button ui-state-default ui-corner-all" onclick="save_all_creditNote('.$creditNote_id.',\'update\')">Save & Applied</button> ';
        if($editView_creditNote && $canEdit)
            $btn_email = '<button class="ui-button ui-state-default ui-corner-all" onclick="popupSendEmail('.$creditNote_id.');return false;">Email</button> ';
        $btn_print = '<button class="ui-button ui-state-default ui-corner-all" onclick="_get_print_credit_note('.$creditNote_id.');return false;">Print</button> ';
        if($canAdd && $addView_creditNote)
            $btn_new = '<button class="ui-button ui-state-default ui-corner-all" onclick="load_creditNote(\'\',\'add\');return false;"><img border="0" src="images/icons/plus.png"> New Credit Note</button> ';
        //$btn_pay = '<button class="ui-button ui-state-default ui-corner-all" onclick="load_payment_creditNote('.$creditNote_arr[0]['customer_id'].','.$creditNote_id.','.$total_amount.');return false;">Payment Applied</button> ';
    }
    else{
        if($editView_creditNote && $canEdit)
            $btn_save = '<button id="creditnote_add" class="ui-button ui-state-default ui-corner-all" onclick="save_all_creditNote(0,\'add\'); return false;">Save & Applied</button> ';
    }
    $btn_back = '<button class="ui-button ui-state-default ui-corner-all" onclick="load_back_creditNote(); return false;">Back</button> ';
    
    echo "<div id='waper_btt_block' style='width:100%; overflow:hidden;border-bottom: 5PX solid #E2E2E2;padding-bottom:2px; padding-top:10px;'>";
        echo $AppUI ->createBlock('btt_block',$btn_back,'style="text-align: center; float:left; height:28px;"');
        echo $AppUI ->createBlock('btt_block',$btn_save.$btn_cancel.$btn_pay.$btn_email.$btn_print.$btn_new, 'style="text-align: center; float: right"'); //Bo button View Quotation
    echo "</div>";
}
function form_info($creditNote_id=""){
    global $AppUI,$CCreditManager,$CCompany,$CSalesManager,$InvoiceManager,$ocio_config;
    require_once (DP_BASE_DIR . '/modules/sales/css/sales.css.php');
    
    if($creditNote_id!=""){
        $creditNote_arr = $CCreditManager->list_db_creditNote ($creditNote_id);
        
        foreach ($creditNote_arr as $creditNote_row) {
            $cre_customer_id = $creditNote_row['customer_id'];
            $cre_co = $creditNote_row['credit_note_co'];
            $cre_address_id = $creditNote_row['address_id'];
            $cre_invoice_id = $creditNote_row['invoice_id'];
            if($cre_invoice_id>0)
            {
                $cre_customer_id=$InvoiceManager->get_invoice_field_by_id($cre_invoice_id, 'customer_id');
            }
            $cre_status = $creditNote_row['credit_note_status'];
            $creditNote_no = $CSalesManager->htmlChars($creditNote_row['credit_note_no']);
            $creditNote_date = date($ocio_config['php_abms_date_format'], strtotime($creditNote_row['credit_note_date']));
            $creditNote_date_value = date('Y-m-d', strtotime($creditNote_row['credit_note_date']));
        }
        // Code khong cho edit
//        if($cre_status==2){
//            $readonly = 'readonly="true"';
//            $disabled = 'disabled';
//            $data_dis = "0";
//        }
        if($cre_status==2){
            $readonly = '';
            $disabled = '';
            $disabled_invoice = 'disabled';
            $style="background:#eee";
            $data_dis = "";
        }
        $SalesAttention_arr = $CSalesManager->get_salesAttention_by_SalesType($creditNote_id,'creditNote');
    }
    else{
        $creditNote_no = $CSalesManager->create_creditNote_or_receipt_no($is_credit=true);
        $creditNote_date = date('d/M/Y');
        $creditNote_date_value = date('Y-m-d');
        if(isset($_POST['invoice_id'])){
            $cre_customer_id = $_POST['customer_id'];
            $cre_address_id = $_POST['address_id'];
            $cre_invoice_id = $_POST['invoice_id'];
            $action = $_POST['action'];
        }
    }
    
    $cre_invoice_rev_arr = $InvoiceManager->get_latest_invoice_revision($cre_invoice_id);
    $cre_invoice_rev_id = $cre_invoice_rev_arr[0]['invoice_revision_id'];
    $view_invoice = "";
    if($cre_invoice_id!="" && $cre_invoice_id!=0)
        $view_invoice = '<a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$cre_invoice_id.'&invoice_revision_id='.$cre_invoice_rev_id.'" >View Invoice</a>';
    
    $credit_status =  dPgetSysVal('CreditStatus');
        
    // Lay thong tin customer
    $customer_arr = $CSalesManager->get_list_companies();
//    print_r($customer_arr);
    $option_customer = '<option value="">--Select Customer--</option>';
    foreach ($customer_arr as $customer_row) {
        $selected = "";
        if($customer_row['company_id']==$cre_customer_id){
            $selected="selected";
        }
        $option_customer .= '<option value='.$customer_row['company_id'].' '.$selected.'>'.$customer_row['company_name'].'</option>';
    }
    
        // Lay thong tin CO
    $co_arr = $CSalesManager->get_list_companies();
    $option_co = '<option value="">--Select Customer--</option>';
    foreach ($co_arr as $co_row) {
        $selected = "";
        if($co_row['company_id']==$cre_co){
            $selected="selected";
        }
        $option_co .= '<option value='.$co_row['company_id'].' '.$selected.'>'.$co_row['company_name'].'</option>';
    }
      
    echo'<table class="tbl" id="credit_note_table" cellspacing="0" border="0" cellpadding="0" width="100%">
        <tr>
            <td>
                <table class="std" cellspacing="0" border="0" cellpadding="4" width="100%">
                    <tbody>
                        <tr>
                            <td width="50%">
                                <table cellspacing="0" border="0" cellpadding="0" width="100%">
                                    <tbody>
                                        <tr>
                                            <td class="detailRowLabel">Customer:</td>
                                            <td class="hilite detailRowField"><select '.$disabled_invoice.' style="padding-top:2px;width:420px;" id="credit_customer"  onchange="load_address_by_customer(this.value)">'.$option_customer.'</select></td>
                                        </tr>
                                        <tr>
                                            <td class="detailRowLabel">C/O:</td>
                                            <td class="hilite detailRowField"><select style="padding-top:2px;width:420px;" id="credit_co"  >'.$option_co.'</select></td>
                                        </tr>
                                        <tr>
                                            <td class="detailRowLabel">Address:</td>
                                            <td class="hilite detailRowField"><select class="text" style="padding-top:2px;width:420px;" onchange="load_infoAddress_by_address(this.value)" id="credit_addres">'.get_address_by_customer($cre_customer_id,$cre_address_id,$action).'</select></td>
                                        </tr>
           <!--                         <tr>
                                            <td class="detailRowLabel">Phone:</td>
                                            <td class="hilite detailRowField" ><div id="credit_tel">'.get_infoAddress_by_address($cre_address_id,"tel").'</div></td>
                                        </tr>
                                        <tr>
                                            <td class="detailRowLabel" style="padding:9px 10px 9px 8px">Fax:</td>
                                            <td class="hilite detailRowField"><div id="credit_fax">'.get_infoAddress_by_address($cre_address_id,"fax").'</div></td>
                                        </tr>
           -->
                                        <tr>
                                            <td class="detailRowLabel">Attention: <input type="hidden" id="sales_attention_id" name="sales_attention_id" value="'.$SalesAttention_arr[0]['sales_attention_id'].'" /> </td>
                                            <td class="hilite detailRowField"><select class="text" name="credit_attention" id="credit_attention" style="padding-top:2px;min-width:150px;" onchange="load_info_attention(this.value);return false;">'.get_attention_by_customer($cre_customer_id,$creditNote_id,$action).'</select></td>
                                        </tr>
                                        <tr>
                                            <td class="detailRowLabel">&nbsp;</td>
                                            <td class="hilite detailRowField"><span id="info_attention">'.  _get_email_by_sales_attention($SalesAttention_arr[0]['attention_id']).'</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <table cellspacing="0" border="0" cellpadding="0" width="100%">
                                    <tbody>
                                        <tr>
                                            <td class="detailRowLabel" style="height:38;">Date:</td>
                                            <td class="hilite detailRowField">
                                                <input id="credit_date_voucher_dislay'.$data_dis.'" name="credit_date_voucher_dislay" readonly="true" value="'.$creditNote_date.'" type="text" class="text" size="10" />
                                                <input id="credit_date_voucher'.$data_dis.'" name="credit_date_voucher" type="hidden" class="text" size="30" value="'.$creditNote_date_value.'" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="detailRowLabel">Voucher No:</td>
                                            <td class="hilite detailRowField" width="80%"><input id="credit_voucher_no" type="text" class="text" value="'.$creditNote_no.'" size="30" /></td>
                                        </tr>
                                        <tr>
                                            <td class="detailRowLabel">Status: </td>
                                            <td class="hilite detailRowField">'.$credit_status[$cre_status].'&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="detailRowLabel">Invoice No:</td>
                                            <td class="hilite detailRowField"><select '.$disabled_invoice.' style="'.$style.'" class="text" id="credit_invoice" onchange="load_date_by_invoice(this.value)">'.  get_invoice_by_customer($cre_customer_id,$cre_invoice_id,$action,$cre_status).'</select><span id="view_invoice">'.$view_invoice.'</span></td>
                                        </tr>
                                        <tr>
                                            <td class="detailRowLabel">Invoice Date:</td>
                                            <td class="hilite detailRowField"><div id="credit_date_invoice">'.  get_date_by_invoice($cre_invoice_id, $action).'</div></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>';
}
function form_table_item($creditNote_id=false){
    global $baseUrl,$AppUI,$CCreditManager,$CSalesManager, $canEdit, $editView_creditNote, $canDelete,$deleteView_creditNote;
    require_once (DP_BASE_DIR."/modules/sales/creditNote.js.php");
    if(!$creditNote_id && isset($_POST['creditNote_id']))
        $creditNote_id = $_POST['creditNote_id'];
    if($canEdit && $editView_creditNote)
        $th_update = '<th width="6%"><input type="checkbox" onclick="check_all(\'item_select_all\', \'item_check_list\');" id="item_select_all" name="item_select_all" ></th>';
    $update=true;
    if($creditNote_id!=""){
        $creditNote_note_id_arr = $CCreditManager->list_db_creditNote_item($creditNote_id);
        $creditNote_arr = $CCreditManager->list_db_creditNote($creditNote_id);
        $invoice_id = $creditNote_arr[0]['invoice_id'];
        $satatus = $creditNote_arr[0]['credit_note_status'];
        // Code khong cho update Item
//        if($satatus ==2){
//            $th_update="";
//            $id_update="";
//            $update=false;
//        }
    }
    echo '<form id="frm_item_creditNote" method="post">';
        echo'<table id="credit_table_item" class="tbl" style="clear: both; width: 100%" cellspacing="1" cellpadding="2" width="100%" >
            <thead>
                <tr>
                    '.$th_update.'
                    <th width="6%">#</th>
                    <th width="78%">Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>';
   
        foreach ($creditNote_note_id_arr as $creditNote_note_id_row) {
            $item_id = $creditNote_note_id_row['credit_note_item_id'];
            if($canEdit && $editView_creditNote)
                    $id_update = '<td align="center">
                        <input type="checkbox" value="'. $item_id .'" name="item_check_list" id="item_check_list">
                        <a class="icon-edit icon-all" onclick="edit_line_creditNoteItem('. $item_id .'); return false;" href="#" title="Inline Edit" style="margin-right:0px"></a>
                    </td>';

            echo'<tr id="row_item_'.$item_id.'">
                    '.$id_update.'
                    <td id="stt_'.$item_id.'" align="center">'.$creditNote_note_id_row['credit_note_item_order'].'</td>
                    <td id="creditNote_des_'.$item_id.'">'.$CSalesManager->htmlChars($creditNote_note_id_row['credit_note_item']).'</td>
                    <td align="right" id="creditNote_amount_'.$item_id.'">$'
                        .number_format($CSalesManager->round_up($creditNote_note_id_row['credit_note_item_amount']),2).'
                        <input type="hidden" value="'.$creditNote_note_id_row['credit_note_item_amount'].'" id="hdd_creditNote_amount_'.$item_id.'" name="hdd_creditNote_amount_'.$item_id.'" />
                    </td>
                </tr>';
        }
            echo'</tbody>
            <tfoot id="add_line_credit_item">
                <input type="hidden" id="count_row_item" value="1" />
            </tfoot>
        </table>';
        echo'<input type="hidden" id="total_creditNote" name="total_creditNote" value="'.count($creditNote_note_id_arr).'">';
        echo'<input type="hidden" id="creditNote_id" name="creditNote_id" value="'.$creditNote_id.'">';
        if($update==true){
            if($canDelete && $deleteView_creditNote)
                $delete_id = '<a class="icon-delete icon-all" onclick="delete_crediNoteItem('.$creditNote_id.'); return false;" href="#">Delete</a>';
            if($creditNote_id=="" || ($canEdit && $editView_creditNote))
                $add_id = '<a class="icon-add icon-all" onclick="add_line_item_redit(); return false;" href="#">Add item</a>';
        }
        echo $block_del = $AppUI ->createBlock('del_block', $delete_id . $add_id, 'style="text-align: left;clear:bold;width:100%;padding:5px 0px 5px 0px;"');
        $totalAmount_arr = $CSalesManager->total_creditNote_amount($creditNote_id);
        
        require_once (DP_BASE_DIR."/modules/sales/CTax.php");
        $CTax = new CTax();
        $tax_arr = $CTax->list_tax();
        if($creditNote_id!=""){
            $creditNote_arr = $CCreditManager->list_db_creditNote($creditNote_id);
            $credit_tax = $creditNote_arr[0]['credit_note_tax_id'];
        }
//        print_r($creditNote_arr);
//        print_r($tax_arr);
        $option_tax = '<option value="">--</option>';
        $tax_value = "0";$total_tax=0;
        foreach ($tax_arr as $tax_row) {
            $selected = "";
            if($tax_row[tax_id]==$credit_tax){
                $selected = "selected";
                $tax_value = $tax_row['tax_rate'];
            }
            $option_tax.='<option value="'.$tax_row['tax_id'].'" '.$selected.'>'.$tax_row['tax_rate'].'</option>';
        }
        
        $sub_total=$CSalesManager->round_up($totalAmount_arr[0]['totalAmount']);
        $total_tax = $sub_total*($tax_value/100);
        if($creditNote_arr[0]['tax_edit_value']!=0)
        {
            $total_tax = $creditNote_arr[0]['tax_edit_value'];
        }
         //Code khong cho edit Tax
//        if($update==true){
//            $select_tax = '<select name="creditNote_tax" onchange="load_update_total_by_tax(this.value)" id="creditNote_tax" class="text">'.$option_tax.'</select>';
//            $edit_tax = ' <input type="text" style="text-align:right;" onchange="load_credit_total();return false;" value='.number_format($total_tax,2).' name="credit_tax_value" id="credit_tax_value" size="10"/><input type="hidden" value="'.$total_tax.'" id="credit_tax_hdd" />';
//        }
//        else{
//            $select_tax = $tax_value;
//            $edit_tax = number_format($total_tax, 2);
//        }
        $select_tax = '<select name="creditNote_tax" onchange="load_update_total_by_tax(this.value)" id="creditNote_tax" class="text">'.$option_tax.'</select>';
        $edit_tax = ' <input type="text" style="text-align:right;" onchange="load_credit_total();return false;" value='.number_format($total_tax,2).' name="credit_tax_value" id="credit_tax_value" size="10"/><input type="hidden" value="'.$total_tax.'" id="credit_tax_hdd" />';
        
        $total = $sub_total + $total_tax;
        
        echo '<table border="0" width="100%" id="credit_item_total">
            <tr><td width="70%"></td>
                <td width="18%" align="right">Sub Total:</td>
                <td align="right" width="12%">
                    <span id="credit_subtotal">$'.  number_format($sub_total,2).'</span>
                    <input type="hidden" value="'.$sub_total.'" id="credit_subtotal_hdd" />
                    <input type="hidden" value="'.$sub_total.'" id="credit_subtotal_hdd_ol" />
                </td>
            </tr>
            <tr>
                <td ></td>
                <td align="right">GST @ '.$select_tax.' %:</td>
                <td align="right">$'.$edit_tax.'
                </td>
            </tr>
            <tr>
                <td></td>
                <td align="right">Total:</td>
                <td align="right">
                    <span id="credit_total">$'.number_format($total,2).'</span>
                    <input type="hidden" value="'.$total.'" id="credit_total_hdd" />
                </td>
                </td>
            </tr>
        </table>';
    echo '</form>';
    $status = "add";
    $add_update = 0;
    if($creditNote_id){
        $status = "update";
        $add_update = $creditNote_id;
    }
    if($satatus!=2)
    {
        if($status=='add')
            $btn_save = '<button id="creditnote_appline" class="ui-button ui-state-default ui-corner-all" onclick="save_all_creditNote('.$add_update.',\''.$status.'\'); return false;">Save & Applied</button> ';
        elseif($canEdit && $editView_creditNote)
            $btn_save = '<button id="creditnote_appline2" class="ui-button ui-state-default ui-corner-all" onclick="save_all_creditNote('.$add_update.',\''.$status.'\'); return false;">Save & Applied</button> ';
    }
    else{
        if($canEdit && $editView_creditNote)
        {
            $status="update_info";
            $btn_save = '<button id="creditnote_save" class="ui-button ui-state-default ui-corner-all" onclick="save_all_creditNote('.$add_update.',\''.$status.'\'); return false;">Save</button> ';
        }
    }
    if($canEdit && $editView_creditNote)
        $btn_cancel = '<button class="ui-button ui-state-default ui-corner-all" onclick="load_back_creditNote(); return false;">Cancel</button> ';
    echo $AppUI ->createBlock('btt_block',$btn_save.$btn_cancel,'style="text-align: center;"');
    echo '<div  class="block_loading loading_active" ></div>';
    echo '<div class="block_loading_content loading_active"><img width="40" src="'.$baseUrl.'/images/ajax-loader.gif" /></div>';
}
function get_address_by_customer($customer_id=false,$address_id=false,$action=false){
    global $CSalesManager;  
    
    $option_address = '<option value="">--Select Address--</option>';
    if(isset($_POST['customer_id']) && $_POST['customer_id']!="")
        $address_arr = $CSalesManager->get_list_address($_POST['customer_id']);
    else if($customer_id!="")
        $address_arr = $CSalesManager->get_list_address($customer_id);
    foreach ($address_arr as $address_row) {
            $selected="";$address_2="";$brand="";$address_type="";
            if($address_id == $address_row['address_id'])
                $selected="selected";
            
            if($address_row['address_type']==2)
                $address_type = "[Billing] ";
            else if($address_row['address_type']==1)
                $address_type = "[Job Site] ";
            else 
                $address_type = "[NA] ";
            $brand="";
            if($address_row['address_branch_name']!="")
                $brand = $address_row['address_branch_name']." - ";
            $address_2 = "";
            if($address_row['address_street_address_2']!="")
                $address_2 = " ".$address_row['address_street_address_2'];
            $option_address.='<option value='.$address_row['address_id'].' '.$selected.'>'.$address_type.$brand.$address_row['address_street_address_1'].$address_2.' '.'Singapore '.$address_row['address_postal_zip_code'].'</option>';
        }
    
    if(!isset($_POST['customer_id']) || $action==1)
        return $option_address;
    else
        echo $option_address;
}
function get_invoice_by_customer($customer_id=false,$invoice_id=FALSE,$action=false,$satus=false){
    global $CSalesManager,$InvoiceManager;
    $option_invoice = '<option value="">--Select Invoice--</option>';
    
    if(isset($_POST['customer_id']) && $_POST['customer_id']!="")
        $invoice_arr = $InvoiceManager->list_invoice($_POST['customer_id'],'');
    else if($customer_id!="")
        $invoice_arr = $InvoiceManager->list_invoice($customer_id,'');
//    print_r($invoice_arr);
    foreach ($invoice_arr as $invoice_row) {
        $invoice_rev_id = $InvoiceManager->get_latest_invoice_revision($invoice_row['invoice_id']);
        // Lay nhung tin voice con no
        $total_show=$InvoiceManager->get_invoice_item_total($invoice_row['invoice_id'], $invoice_rev_id[0]['invoice_revision_id']);
        $amount_due = $InvoiceManager->get_total_tax_and_paid($invoice_rev_id[0]['invoice_revision_id'], $total_show);
        // End
        $selected = "";
        if($invoice_id==$invoice_row['invoice_id'])
                $selected = "selected";
        $option_invoice .= '<option value="'.$invoice_row['invoice_id'].','.$invoice_rev_id[0]['invoice_revision_id'].'" '.$selected.'>'.$invoice_row['invoice_no'].'</option>';
    }
    if(!isset($_POST['customer_id']) || $action==1)
        return $option_invoice;
    else 
        echo $option_invoice;
}
function get_infoAddress_by_address($address_id=false,$info_type=FALSE){
    global $CSalesManager,$InvoiceManager;
    
    if(isset($_POST['info']))
        $info_type = $_POST['info'];
    if(isset($_POST['address_id']))
        $info_arr=$CSalesManager->get_address_by_id($_POST['address_id']);
    else
        $info_arr=$CSalesManager->get_address_by_id($address_id);
//    print_r($info_arr);
    $info_value="";
        if($info_type=="tel"){
            if($info_arr['address_phone_1']!="")
                $info_value=$info_arr['address_phone_1'].'.&nbsp;&nbsp;&nbsp&nbsp;&nbsp&nbsp';
            $info_value.=$info_arr['address_phone_2'];
        }
        else
            $info_value=$info_arr['address_fax'];
    if(isset($_POST['info']))
        echo $info_value;
    else
        return $info_value;
}
function get_date_by_invoice($invoice_id=false,$action=false){
    global $CSalesManager, $InvoiceManager;
    $date_invoice = "";
    if(isset($_POST['invoice_id']))
        $invoice_arr = $InvoiceManager->get_db_invoice($_POST['invoice_id']);
    else
        $invoice_arr = $InvoiceManager->get_db_invoice($invoice_id);
//    print_r($invoice_arr);
    foreach ($invoice_arr as $invoice_row) {
        $date_invoice = date('d/m/Y', strtotime($invoice_row['invoice_date']));
    }
    if(!isset($_POST['invoice_id']) || $action==1)
        return $date_invoice;
    else
        echo $date_invoice;
}
function _do_save_creditNote(){
    global $AppUI, $CCreditManager,$CSalesManager;
    $customer_id = $_POST['customer_id'];
    $credit_co = $_POST['credit_co'];
    $address_id = $_POST['address_id'];
    $attention_id = $_POST['attention_id'];
    $credit_voucher_no = $_POST['credit_voucher_no'];
    $credit_date_voucher = $_POST['credit_date_voucher'];
    $invoice_id = $_POST['invoice_id'];
    $creditNote_tax = $_POST['creditNote_tax'];
    $creditNote_tax_edit_value = $_POST['creditNote_tax_edit_value'];
    $creditNote_tax_edit_value = str_replace(',', '', $creditNote_tax_edit_value);
    $creditNote_tax_edit_value = $CSalesManager->round_up($creditNote_tax_edit_value);
    $exist_voucher_no = $CCreditManager->get_exist_creditNote_no($credit_voucher_no);
//    // kiem tra ton tai Voucher No
//    $voucher_no = $CCreditManager->get_exist_creditNote_no($credit_voucher_no);
    $objCredit['credit_note_id'] = 0;
    $objCredit['invoice_id'] = $invoice_id;
    $objCredit['customer_id'] = $customer_id;
    $objCredit['address_id'] = $address_id;
    $objCredit['credit_note_no'] = $credit_voucher_no;
    $objCredit['credit_note_date'] = $credit_date_voucher;
    $objCredit['credit_note_status'] = 1;
    $objCredit['credit_note_tax_id'] = $creditNote_tax;
    $objCredit['tax_edit_value'] = $creditNote_tax_edit_value;
    $objCredit['credit_note_co'] = $credit_co;
    $masage = "";
    if($exist_voucher_no == true){
        $credit_id = 0;
        $masage="This Credit Note already exists";
    }
    else{
       
        $credit_id = $CCreditManager->add_creditNote($objCredit);
    }
   
    if($credit_id>0){
        _do_add_salesAttention(0,$credit_id,$attention_id,"creditNote");
        echo '{"status":"Success","credit_note_id":'.$credit_id.'}';
        
    }
    else if($credit_id==0)
        echo '{"status":"Exist","masage":"'.$masage.'"}';
    else
        echo '{"status":"Failure"}';
}
function _do_save_creditNote_item(){
    global $CCreditManager;
    $order_credit_arr = $_POST['order_credit'];
    $dis_credit_arr = $_POST['dis_credit'];
    $amount_credit_arr = $_POST['amount_credit'];
    
    for($i=0;$i<count($order_credit_arr);$i++){
        $itemOjb = array();
        $itemOjb['credit_note_item_id'] = 0;
        $itemOjb['credit_note_id'] = $_POST['credit_note_id'];
        $itemOjb['credit_note_item_order'] = $order_credit_arr[$i];
        $itemOjb['credit_note_item'] = $dis_credit_arr[$i];
        $itemOjb['credit_note_item_amount'] = $amount_credit_arr[$i];
        $credit_note_item_id = $CCreditManager->add_creditNote_item($itemOjb);
    }
    
    if(count($credit_note_item_id)>0)
        echo '{"status":"Success"}';
    else{
        echo '{"status":"Failure"}';
    }
}
function _do_update_creditNote(){
    global $CCreditManager,$CSalesManager;
    $tax_edit_value = $_POST['creditNote_tax_edit_value'];
    $tax_edit_value = str_replace(',', '', $tax_edit_value);
    $tax_edit_value = $CSalesManager->round_up($tax_edit_value);
    $attention_id = $_POST['attention_id'];
    $sales_attention_id = $_POST['sales_attention_id'];
    
    $creditNote_arr=$CCreditManager->update_creditNote($_POST);
    _do_add_salesAttention($sales_attention_id,$_POST['creditNote_id'],$attention_id,"creditNote");
    $response=array('status'=>'Success');
    echo  json_encode($response);
//    if($creditNote_arr)
//        echo '{"status":"Success"}';
//    else
//        echo '{"status":"Fail"}';
}
function _do_delete_crediNote_item(){
    global $CCreditManager;
    $creditNote_item_id_arr="(".$_POST['crediNote_item_arr'].")";
    $creditNote_arr = $CCreditManager->delete_creditNote_item($creditNote_item_id_arr);
    if($creditNote_arr==true)
        echo '{"status":"Success"}';
    else
        echo '{"status":"Failure"}';
}
function _do_update_status_creditNote(){
    global $CCreditManager;
    $creditNote_id = $_POST['creditNote_id'];
    $credit_note_status = $_POST['credit_note_status'];
    if(isset($_POST['invoice_id']))
        $creditNote_arr = $CCreditManager->update_credit_note_status($creditNote_id, $credit_note_status,$_POST['invoice_id']);
    else
        $creditNote_arr = $CCreditManager->update_credit_note_status($creditNote_id, $credit_note_status);
    if(count($creditNote_arr)>0)
        echo '{"status":"Success"}';
    else
        echo '{"status":"Failure"}';
}
function _do_delete_creditNote(){
    global $CCreditManager;
    $creditNote_id_arr = '('.$_POST["crediNote_item_arr"].')';
    $creditNote_del = $CCreditManager->delete_credit_note($creditNote_id_arr);
    if($creditNote_del==true)
        echo '{"status":"Success"}';
    else
        echo '{"status":"Failure"}';
}
function _form_print(){
    $select = '<div id="tbl_generate"><br/>
        <form id="generate_value" mothod="POST" action="" name="generate_value">';
            $select .= '<span>Are you sure print to</span>&nbsp; &nbsp;';
            $select .= '<select id="generate" name="generate" class="text">
                    <option value="0">HTML</option>
                    <option value="1" selected>PDF</option>
                </select>&nbsp; &nbsp;
                <span>this Credit Note?</span>';
    $select .= '</form></div>';
    echo $select;
}
function _do_print_pdf(){
    global $CCreditManager;
    $creditNote_id = $_REQUEST['creditNote_id'];
    $CCreditManager->create_view_print_pdf($creditNote_id);
}
function _form_send_email(){
    global $AppUI,$CCreditManager;
    require_once($AppUI->getSystemClass( 'libmail' ));
    $mail = new Mail; // create the mail
    $creditNote_id = $_REQUEST['creditNote_id'];
    $creditNote_arr = $CCreditManager->list_db_creditNote($creditNote_id);
    $creditNote_no = $creditNote_arr[0]['credit_note_no'];
    $content = 'chua config noi dung';
    $from = "";
    $to = "";
    $att = $CCreditManager->create_view_print_pdf($creditNote_id, true);
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
            <input id="file_attach" type="text" name="name_file" value="<?php echo $creditNote_no .'.pdf';?>" readonly="true">
             <input class="mainbutton" type="submit" onclick="print_credit(<?php echo $creditNote_id; ?>); return false;" value="Download" name="dowload_file">
            </td>
        </tr>
        
        <tr>
            <td align="center" colspan="2">
            <input class="mainbutton" type="button" onclick="setSendButton(<?php echo $creditNote_id; ?>)" value="Send" name="send" />
            <input class="mainbutton" type="reset" value="Reset" name="back" />
            </td>
        </tr>
        
        </table>
	</form>

    <?php
}
function _do_send_email_invoice() {
        global $CCreditManager;
        if ($_POST['action'] == 'send_mail_in') {
            $invoice_id_array = $_POST['creditNote_id'];
            $content = $_POST['content'];
            $reciver = $_POST['reciver'];
            $sender = $_POST['sender'];
            $subject = $_POST['subject'];

            $status = $CCreditManager->send_email_credit($invoice_id_array, $content, $sender, $subject, $reciver);
        }
        if ($status)
                echo '{"status": "Success", "message": "Email sent"}';
            else
                echo '{"status": "Failure", "message": "Email sent error"}';
}
function _do_load_view_invoice(){
    $invoice_id = $_POST['invoice_id'];
    $invoice_rev_id = $_POST['invoice_rev_id'];
    echo '<a href="?m=sales&show_all=1&tab=1&status_rev=update&invoice_id='.$invoice_id.'&invoice_revision_id='.$invoice_rev_id.'" >View Invoice</a>';
}
function _do_save_edit_item_ctedit(){
    global $CCreditManager;
    $credit_item_id = $_POST['creditNote_id']; 
    $credit_order = $_POST['creditItem_order'];
    $credit_des = $_POST['creditItem_des'];
    $credit_amount = $_POST['creditItem_amount'];
    
    $creditItem_arr = $CCreditManager->update_credit_item($credit_item_id, $credit_des, $credit_amount, $credit_order);
    if($creditItem_arr>0)
        echo '{"status": "Success"}';
    else
        echo '{"status": "Failure"}';
}
function _do_print_html(){
    global $CCreditManager;
    $creditNote_id = $_REQUEST['creditNote_id'];
    $CCreditManager->create_print_html($creditNote_id);
}
function get_attention_by_customer($customer_id=false,$creditNote_id=false,$action=false){
    global $CSalesManager;
    if(isset($_POST['customer_id']) && $_POST['customer_id']!="")
        $customer_id = $_POST['customer_id'];
    $attention_arr = $CSalesManager->get_list_attention($customer_id);
    
    if($creditNote_id)
        $SalesAttention_arr = $CSalesManager->get_salesAttention_by_SalesType($creditNote_id,'creditNote');
    
    $attention_option = '<option value="">--Select Attention--</option>';
    foreach ($attention_arr as $attention_row) {
        $select = "";
        if($attention_row['contact_id']==$SalesAttention_arr[0]['attention_id'])
            $select = "selected";
        $attention_option.='<option '.$select.' value="'.$attention_row['contact_id'].'">'.$attention_row['contact_first_name'] .' '. $attention_row['contact_last_name'].'</option>';
    }
    if(!isset($_POST['customer_id']) || $action==1)
        return $attention_option;
    echo $attention_option;
}
function _do_add_salesAttention($sales_attention_id,$type_id,$attention_id,$type_name){
    global $CSalesManager;
    $salesAtention['sales_attention_id']=$sales_attention_id;
    $salesAtention['sales_type_id'] = $type_id;
    $salesAtention['attention_id'] = $attention_id;
    $salesAtention['sales_type_name'] = $type_name;
    
    $salesAtention_id = $CSalesManager->add_salesAttention($salesAtention);
}
// Load ra Email
function _get_email_by_sales_attention($attention_id=false){
    global $CSalesManager;
    //return $attention_id;
    if(isset($_POST['attention_id'])){
        $attention_id = $_POST['attention_id'];
    }
  
    $email_rr = $CSalesManager->get_attention_email($attention_id);
    $email="";
    if(count($email_rr) >0){
        if($email_rr['contact_email']!="")
            $email .= "Email: ".$email_rr['contact_email']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        if($email_rr['contact_mobile']!="")
            $email .= "Mobile: ".$email_rr['contact_mobile'];
    }
    
    if(!isset($_POST['attention_id']))
        return $email;
    
    echo $email;
}

?>
