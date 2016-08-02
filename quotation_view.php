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
    echo '<div>';
    echo $block_note = $AppUI ->createBlock('div_note', _form_note(),'style="float: right;margin-right : 40%"');
    echo '</div>';
     echo '</br>';     
     echo $block_button = $AppUI ->createBlock('div_bb', _form_save(),'style="float: center;margin-right : 40%"');

     }
function _form_info(){
    global $AppUI;
    echo $tittle_block = $AppUI ->createBlock('div_details','<h1>Quotation</h1>','style="text-align: center;text-font:bold; float: left; margin-right : 10 px"');
    echo '<br><br>';
    echo $tittle_block = $AppUI ->createBlock('div_supplier','Supplier’s Name','style="float: left; margin-left : 40%"');
    echo '<br>';
    echo $tittle_block = $AppUI ->createBlock('div_1','Supplier’s Address','style="float: left; margin-left : 40%"');
    echo '<br>';
    echo $tittle_block = $AppUI ->createBlock('div_2','Supplier’s phone, fax, email, website','style="float: left; margin-left : 40%"');
    echo '<br>';
    echo $tittle_block = $AppUI ->createBlock('div_3','Reg No: Supplier’s reg no','style="float: left; margin-left : 40%"');
    echo '<br>';
    echo $tittle_block = $AppUI ->createBlock('div_4','GST Reg No:Supplier’s GST reg no','style="float: left; margin-left : 40%"');

    echo '<br><br>';

}
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
    // END OF RAW TABLE

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
       
        foreach ($rows as $row) {
                $list_client= $row['company_name'];                
            }
       echo $client_block = $AppUI ->createBlock('div_client','Client:  '.$list_client,'style="float: left"');
       echo '<br>';
       echo $add_block = $AppUI ->createBlock('div_client','Hanoi, Vietnam','style="float: left"');
    
       $quo_no = 'Quatation No: Q-001';
       $date = 'Date: 24 May,2011';
     echo $quo = $AppUI ->createBlock('div_no',$quo_no,'style="float: right;margin-right : 40%"');
     echo '<br>';
     echo $date = $AppUI ->createBlock('div_date', $date,'style="float: right;margin-right : 40%"');
     
    // Add the section to the page (place it inside)
        $demonstrationSection->style = 'height: 500';
        $demonstrationForm->style = 'height: 500';
        $demonstrationForm->addJFormSection($demonstrationSection);

        // Add the page to the form (place it inside)
        //$demonstrationForm->addJFormPage($demonstrationPage);

        // Process any request to the form
        $demonstrationForm->processRequest($to_generate);
        $demonstrationForm->clearAllComponentValues();

       
       }

function _form_new_process() {
    _form_new(false);
      
}


function save_table_inline_add1() {
   
    $result = array();
    $result['status'] = 'success';
   
    echo json_encode($result);
}

function update_field1() {
    // get record id through POST:
    $id = dPgetCleanParam($_POST, 'id');

    // get updated content through POST:
    $updated_content = dPgetCleanParam($_POST, 'value');

    // get col name to be updated through GEt:
    $col_name = dPgetCleanParam($_GET, 'col_name', '');

  
}

function show_ajax_table() {
    // AJAX TABLE
    $optionArray = array();
    $optionArray['sAjaxSource'] = '?m=sales&c=get_table1_data_ajax1&suppressHeaders=true';
    $optionArray['bServerSide'] = 'true';
    $optionArray['sPaginationType'] = 'full_numbers';
    $optionArray['bFilter'] = 'false';
    $optionArray['bSearchable'] = 'false';
    $optionArray['bInfo'] = 'false';
    $optionArray['bPaginate'] = 'false';

    $dataTableAjax = new JDataTable('ajax_data_table', $optionArray);
    $dataTableAjax->setWidth('700px');
    //$dataTableAjax->setHeaders(array('Field Name', 'Field Value', 'Field Value\'s Description'));
    $dataTableAjax->setHeaders(array('', '', ''));
    $dataTableAjax->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
    echo $dataTableAjax->show();
    // END AJAX TABLE
}

function _form_note() {
     global $AppUI;
    $textarea = new JFormComponentTextArea('notes', '', array(
                 'width' => '300',
	            'height' => '80',
	            'validationOptions' => array('required'),
            ));

    $note = '1. Why do you need to learn English? <br> 2. More and more have a people using English as a second language';
    $terms = '1. Practice and confidence is important when learning English? <br> 2. More and more have a people using English as a second language';
    echo $note_block = $AppUI ->createBlock('div_notes','<b>Notes </b><br> '.$note.'<br><b>Terms and Conditions</b><br>'.$terms,'style="float: left"');
   // echo $terms_block = $AppUI ->createBlock('div_terms','<b>Terms and Conditions</b><br>'.$terms,'style="float: left"');
 }

 function _form_save() {
     global $AppUI;
echo '<br><br><br><br><br>';
    $save_block = $AppUI ->createBlock('button_save','<button class="ui-button ui-state-default ui-corner-all"  onclick="">Save</button>&nbsp;&nbsp;','style=" float: left "');
      $print_block = $AppUI ->createBlock('button_print','<button class="ui-button ui-state-default ui-corner-all"  onclick="">Cancel</button>&nbsp;&nbsp;','style=" float: left "');
      $print_block = $AppUI ->createBlock('button_print','<button class="ui-button ui-state-default ui-corner-all"  onclick="">Print</button>&nbsp;&nbsp;','style=" float: left "');
      $email_block = $AppUI ->createBlock('button_email','<button class="ui-button ui-state-default ui-corner-all"  onclick="">Email</button>&nbsp;&nbsp;','style=" float: left "');
       echo $btt_block = $AppUI ->createBlock('btt_block','<br>'.$save_block.$print_block.$email_block,'style="text-align: left; float: left"');

 }


// Set the function for a successful form submission
// so if all validation passes on all form components, the data gets sent server side to be handled by this function, in this function is where you would want to put all your handling,
// whether that is talking to a database, checking of login credentials, here is the place you do with your data that gets submitted.


?>

