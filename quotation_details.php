<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once (DP_BASE_DIR."/modules/clients/company_manager.class.php");
function __default() {
    global $AppUI;
    echo $block_info = $AppUI ->createBlock('div_info',  _form_info(),'style="float: left"');
    echo $block_new = $AppUI ->createBlock('div_new',  _form_new(),'style="float: center"');
    echo '<br>';
    echo '<br>';
    show_html_table();
   
    echo $block_total = $AppUI ->createBlock('div_total',  _form_total(),'style="float: right;margin-right : 40%"');
    echo $block_note = $AppUI ->createBlock('div_note', _form_note(),'style="float: right;margin-right : 40%"');
    echo '<br>';
    echo $block_note = $AppUI ->createBlock('div_note',_form_button(),'style="float: right;margin-right : 40%"');

     }
//function _form_info(){
//    global $AppUI;
//    echo $tittle_block = $AppUI ->createBlock('div_details','<h1>New Quotation</h1>','style="text-align: center;text-font:bold; float: left; margin-right : 10 px"');
//    echo '<br><div style="width:60%; height: 5px; align: left; background-color: #E2E2E2"></div><br>';
//    echo $tittle_block = $AppUI ->createBlock('div_supplier','Supplier’s Name','style="float: left; margin-left : 40%"');
//    echo '<br>';
//    echo $tittle_block = $AppUI ->createBlock('div_1','Supplier’s Address','style="float: left; margin-left : 40%"');
//    echo '<br>';
//    echo $tittle_block = $AppUI ->createBlock('div_2','Supplier’s phone, fax, email, website','style="float: left; margin-left : 40%"');
//    echo '<br>';
//    echo $tittle_block = $AppUI ->createBlock('div_3','Reg No: Supplier’s reg no','style="float: left; margin-left : 40%"');
//    echo '<br>';
//    echo $tittle_block = $AppUI ->createBlock('div_4','GST Reg No:Supplier’s GST reg no','style="float: left; margin-left : 40%"');
//    echo '<br><br>';
//    echo $contact_block = $AppUI -> createBlock('div_5','<b>Sales Contact</b><br> 123 Ha noi, Vn','style="float: left; margin-left : 40%"');
//    echo '<br><br>';
//}
     /*
      * Form info - NA
      */
     function _form_info() {

    global $AppUI;

    $supplier = '<p>Supplier’s Name</p>';
    $supplier .= '<p>Supplier’s Address</p>';
    $supplier .= '<p>Supplier’s phone, fax, email, website</p>';
    $supplier .= '<p>Reg No: Supplier’s reg no</p>';
    $supplier .= '<p>GST Reg No:Supplier’s GST reg no</p>';

    $quotation_info = '<p>Date: <input id="add_client" type="text" class="text" name="add_client"></p>';
    $quotation_info .= '<p>Quotation No: <input id="add_client" type="text" class="text" name="add_client"></p>';
    $quotation_info .= '<p>Quotation Rev: <input id="add_client" type="text" class="text" name="add_client"></p>';


    $div_supplier = $AppUI ->createBlock('div_logo_supplier', $supplier, '');
    $div_quotation_info = $AppUI ->createBlock('div_quotation_info', $quotation_info, '');
    
    $div_logo_left = $AppUI ->createBlock('div_logo_left', '', 'style="float: left; width: 550px; height:170px;"');
    
    $div_logo_right = $AppUI ->createBlock('div_logo_right', $div_supplier . $div_quotation_info, 'style="float: right; width: 250px;"');

    echo '<div id="div_info_1">' . $div_logo_left . $div_logo_right . '</div>';


    $cManager = new CompanyManager;
    $rows = $cManager->getCompanies(false, -1);
    $client_option = '';
    foreach ($rows as $row) {
        $client_option .= '<option value='. $row['company_id'].'">'. $row['company_name'] .'</option>';
    }

    $dropdown_client = '<select class="text" name="client" id="client">'. $client_option .'</select>';

    $client = '<p><b>Bill To: '.$dropdown_client.'</b></p>';
    $cash = '<p>Type of Supply: </p>';
    $cash .= '<p>Attention: </p>';

    $div_2_client = $AppUI ->createBlock('div_2_client', $client, '');
    $div_2_cash = $AppUI ->createBlock('div_2_cash', $cash, '');

    $sales_contact = '<p><b>Sales Contact</b></p>';
    $sales_contact .= '<p>Name: <input id="add_client" type="text" class="text" name="add_client"></p>';
    $sales_contact .= '<p>Email: <input id="add_client" type="text" class="text" name="add_client"></p>';
    $sales_contact .= '<p>Phone: <input id="add_client" type="text" class="text" name="add_client"></p>';

    $div_2_left = $AppUI ->createBlock('div_2_left', $div_2_client . $div_2_cash, 'style="float: left; width: 550px;"');
    $div_2_right = $AppUI ->createBlock('div_2_right', $sales_contact, 'style="float: right; width: 250px;"');

    echo '<div id="div_info_2">' . $div_2_left . $div_2_right . '</div>';

}

     //NA - end
     
function show_html_table() {   
    $dataTableRaw = new JDataTable('detail_quotation_table');
    $dataTableRaw->setWidth('60%');
    $dataTableRaw->setHeaders(array('#','Item', 'Quantity','Price','Total'));
    $tableData = array();
    $tableData[] = array('1', 'Ipad<br><font color="#AAAAAA">description</font>','100','10','1000');
        
    $colAttributes = array('class="c_stt" width="5%" align="center"', 'class="c_item" width="25%"', 'class="f_price" width="10%" align="center"', 'class="f_qty" width="10%" align="center"', 'class="f_total" width="10%" align="right"');
    $dataTableRaw->setDataRow($tableData, $colAttributes);
    $dataTableRaw->setJEditable('f_price', '?m=sales&c=update_field&suppressHeaders=true&col_name=Price');
    $dataTableRaw->setJEditable('f_qty', '?m=sales&c=update_field&suppressHeaders=true&col_name=Price');
    $dataTableRaw->setJEditable('c_item', '?m=sales&c=update_field&suppressHeaders=true&col_name=Price');
    $newFormElements = array(
         'field_stt'    => array(
             'type'  => ''),
         'field_name'    => array(
             'type'  => 'text'),
         'field_qty'   => array(
             'type'  => 'text'),
         'field_price'=> array(
             'type'  =>  'text'),
        'field_t'    => array(
            ),
     );
    $dataTableRaw->setDynamicAddFormRow('New Item',
                $newFormElements,
                'index.php?m=sales&c=save_table_inline_add&suppressHeaders=true');
    $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
    $dataTableRaw->show();

}

function save_table_inline_add() {

}

function update_field() {
    $id = dPgetCleanParam($_POST, 'id');
    $updated_content = dPgetCleanParam($_POST, 'value');

    // get col name to be updated through GEt: tên cột trong CSDL.
    $col_des = dPgetCleanParam($_GET, 'description', '');

    // update: Thực hiện update.
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
function _form_total(){

    global $AppUI;
    echo $total = $AppUI ->createBlock('div_total','Total : ','style="float: right; margin-right : 50%"');
    echo '<br>';
    echo $tittle_block = $AppUI ->createBlock('div_1','Add GST: ','style="float: right; margin-right :50%"');
    echo '<br>';
    echo $tittle_block = $AppUI ->createBlock('div_2','Amount Due : ','style="float: right; margin-right : 50%"');
    echo '<br><br>';
        
}

function _form_new() {
    global $AppUI;
    $_from_new_id = 'demonstrationForm';
    $demonstrationForm = new JFormer($_from_new_id,
                    array(
            ));
    $demonstrationSection = new JFormSection($demonstrationForm->id . 'Section');
        $cManager= new CompanyManager;
        $rows = $cManager->getCompanies(false,-1);
        echo 'Client :  <select class="text">';
        foreach ($rows as $row) {
                $list_client= $row['company_name'];
                $client_drop = '<option  value='. $row['company_id'].'">'.$list_client.'</option>';
                echo $client_block = $AppUI ->createBlock('div_client',$client_drop,'style="float: left"');
            }
    $address_client = new JFormComponentSingleLineText('add_client', 'Address:', array(
                             'validationOptions' => array('required'),
            ));
           
            echo '<option value="">New Client...</option></select><br> ';
            echo $client_add = $AppUI ->createBlock('div_add',$address_client,'style="float: left"');
    $demonstrationSection->addJFormComponentArray(array(
        $quo_no = new JFormComponentSingleLineText('quo-no', 'Quatation No:', array(
                             'validationOptions' => array('required'),
            )),
        $date = new JFormComponentDate('id_date', 'Date:', array(
                'validationOptions' => array('required'),
        )),
     ));

     echo $quo = $AppUI ->createBlock('div_no',$quo_no,'style="float: right;margin-right : 40%"');
     echo '<br>';
     echo $date = $AppUI ->createBlock('div_date', $date,'style="float: right;margin-right : 40%"');

        $demonstrationSection->style = 'height: 500';
        $demonstrationForm->style = 'height: 500';
        $demonstrationForm->addJFormSection($demonstrationSection);
        $demonstrationForm->processRequest($to_generate);
        $demonstrationForm->clearAllComponentValues();

       }

function _form_note() {
     global $AppUI;
    $textarea = new JFormComponentTextArea('notes', '', array(
                 'width' => '350',
	            'height' => '80',
	            'validationOptions' => array('required'),
            ));
     $textarea1 = new JFormComponentTextArea('notes', '', array(
                 'width' => '250',
	            'height' => '80',
	            'validationOptions' => array('required'),
            ));
    echo $note_block = $AppUI ->createBlock('div_notes','Notes '.$textarea,'style="float: left"');
    echo $terms_block = $AppUI ->createBlock('div_terms','Terms and Conditions'.$textarea1,'style="float: right;margin-right : 40%"');
}

function _form_button() {
     global $AppUI;
      $back_block = $AppUI ->createBlock('back','<button class="ui-button ui-state-default ui-corner-all"  onclick="">Back</button>&nbsp;&nbsp;','style=" float: left "');
      $save_block = $AppUI ->createBlock('save','<button class="ui-button ui-state-default ui-corner-all"  onclick="">Save</button>&nbsp;&nbsp;','style=" float: left "');
      $print_block = $AppUI ->createBlock('print','<button class="ui-button ui-state-default ui-corner-all"  onclick="">Print</button>&nbsp;&nbsp;','style=" float: left "');
      $email_block = $AppUI ->createBlock('email','<button class="ui-button ui-state-default ui-corner-all"  onclick="">Email</button>&nbsp;&nbsp;','style=" float: left "');
      $cencel_block = $AppUI ->createBlock('cencel','<button class="ui-button ui-state-default ui-corner-all"  onclick="">Cancel</button>&nbsp;&nbsp;','style=" float: left "');
       $create_invoice = $AppUI ->createBlock('create_invoice','<button class="ui-button ui-state-default ui-corner-all"  onclick="">Create Invoice</button>&nbsp;&nbsp;','style=" float: left "');
      echo $btt_block = $AppUI ->createBlock('btt_block','<br>'.$back_block.$save_block.$cencel_block.$print_block.$email_block.$create_invoice,'style="text-align: center; float: left"');
}

?>

