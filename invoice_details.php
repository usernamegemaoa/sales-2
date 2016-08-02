<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/clients/company_manager.class.php");

function __default() {

    global $AppUI;

    include DP_BASE_DIR."/modules/sales/css/invoice.css.php";

    echo '<div id="div_invoice">';
        echo '<div id="div_details" style="width:800px;">';

            echo '<div id="div_0">';
                _form_button();
            echo '</div>';
            
            echo '<div id="div_info">';
                _form_info();
            echo '</div>';

            //echo '<div id="div_2">';
              //  _form_new();
            //echo '</div>';

            echo '<div id="div_3">';
                show_html_table();
            echo '</div>';
            
            echo '<div id="div_4">';
                _form_total();
            echo '</div>';

            echo '<div id="div_5">';
                _form_note();
            echo '</div>';
            
        echo '</div>';
    echo '</div>';
}

function _form_button() {

     global $AppUI;

      echo $AppUI ->createBlock('div_title', '<h1>New Invoice</h1>', 'style="text-align: center; text-font:bold; float: left;"');
      
      $title = '';

      $back_block = '<button class="ui-button ui-state-default ui-corner-all" onclick="">Back</button>&nbsp;';
      $change_rev_block = '<button class="ui-button ui-state-default ui-corner-all"  onclick="">Change Revision</button>&nbsp';
      $save_block = '<button class="ui-button ui-state-default ui-corner-all"  onclick="">Save All</button>&nbsp';
      $print_block = '<button class="ui-button ui-state-default ui-corner-all"  onclick="">Print</button>&nbsp;';
      $email_block = '<button class="ui-button ui-state-default ui-corner-all"  onclick="">Email</button>&nbsp;';
      $cencel_block = '<button class="ui-button ui-state-default ui-corner-all"  onclick="">Cancel</button>';

      echo $AppUI ->createBlock('btt_block', $back_block. $change_rev_block . $save_block . $print_block . $email_block . $cencel_block, 'style="text-align: center; float: right"');
      
}

function _form_info() {

    global $AppUI;

    $supplier = '<p>Supplier’s Name</p>';
    $supplier .= '<p>Supplier’s Address</p>';
    $supplier .= '<p>Supplier’s phone, fax, email, website</p>';
    $supplier .= '<p>Reg No: Supplier’s reg no</p>';
    $supplier .= '<p>GST Reg No:Supplier’s GST reg no</p>';

    $invoice_info = '<p>Date: <input id="add_client" type="text" class="text" name="add_client"></p>';
    $invoice_info .= '<p>Invoice No: <input id="add_client" type="text" class="text" name="add_client"></p>';
    $invoice_info .= '<p>Invoice Rev: <input id="add_client" type="text" class="text" name="add_client"></p>';


    $div_supplier = $AppUI ->createBlock('div_logo_supplier', $supplier, '');
    $div_invoice_info = $AppUI ->createBlock('div_invoice_info', $invoice_info, '');
    
    $div_logo_left = $AppUI ->createBlock('div_logo_left', '', 'style="float: left; width: 550px; height:170px;"');
    
    $div_logo_right = $AppUI ->createBlock('div_logo_right', $div_supplier . $div_invoice_info, 'style="float: right; width: 250px;"');

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

function _form_new() {

    global $AppUI;

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

    echo $div_2_left . $div_2_right;

}

function show_html_table() {
    $dataTableRaw = new JDataTable('detail_invoice_table');
    $dataTableRaw->setWidth('100%');
    $dataTableRaw->setHeaders(array('#','Item', 'Quantity','Price','Amount'));

    $tableData = array();
    $tableData[] = array('1', 'Ipad<br><font color="#AAAAAA">description</font>','100','10','1000');

    $colAttributes = array('class="c_stt" width="5%" align="center"', 'class="c_item" width="25%"', 'class="f_price" width="10%" align="center"', 'class="f_qty" width="10%" align="center"', 'class="f_total" width="10%" align="right"');
    $dataTableRaw->setDataRow($tableData, $colAttributes);
    $dataTableRaw->setJEditable('f_price', '?m=sales&c=update_field&suppressHeaders=true&col_name=Price');
    $dataTableRaw->setJEditable('f_qty', '?m=sales&c=update_field&suppressHeaders=true&col_name=Price');
    $dataTableRaw->setJEditable('c_item', '?m=sales&c=update_field&suppressHeaders=true&col_name=Price');
     $newFormElements = array(
            'field_stt'    => array(),
            'field_name'    => array(
                'type'  => 'text',
                'dateFormat'    => ''),
            'field_value'   => array(
                'type'  => 'text',
                'dateFormat'    => ''),
            'field_dropdown'=> array(
                'type'  => 'text',
                'dateFormat'    => ''),
        );
    $dataTableRaw->setDynamicAddFormRow('New Item',
                $newFormElements,
                'index.php?m=sales&c=save_table_inline_add1&suppressHeaders=true');

    $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
    $dataTableRaw->show();
}
   
function _form_total(){

    global $AppUI;


    $table = '<table boder="0" width="100%" class="tbl_total">
        <tr>
            <td class="td_left">Total :</td>
            <td class="td_right"></td>
        </tr>
        <tr>
            <td class="td_left">Tax @ 7% :</td>
            <td class="td_right"></td>
        </tr>
        <tr>
            <td class="td_left">Add GST :</td>
            <td class="td_right"></td>
        </tr>
        <tr>
            <td class="td_left">Amount Paid :</td>
            <td class="td_right"></td>
        </tr>
        <tr>
            <td class="td_left">Amount Due :</td>
            <td class="td_right"></td>
        </tr>

        </table>';

    echo $table;
}

function _form_note() {
     global $AppUI;
    $textarea = new JFormComponentTextArea('notes', '', array(
                 'width' => '350',
	            'height' => '80',
	            'validationOptions' => array('required'),
            ));
     $textarea1 = new JFormComponentTextArea('notes', '', array(
                 'width' => '350',
	            'height' => '80',
	            'validationOptions' => array('required'),
            ));
    echo $note_block = $AppUI ->createBlock('div_notes','Notes '.$textarea,'style="float: left;"');
    echo $terms_block = $AppUI ->createBlock('div_terms','Terms and Conditions'.$textarea1,'style="float: right;"');
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

?>

