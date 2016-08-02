<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/sales/CTemplateManager.php");
include DP_BASE_DIR."/modules/sales/template.js.php";
$CTemplateManager = new CTemplateManage();

function __default() {
    global $AppUI;
    $template_btn = '<button class="ui-button ui-state-default ui-corner-all" onclick="back_list_template(); return false;">Template</button>&nbsp;&nbsp;';
    $term_condition_btn = '<button class="ui-button ui-state-default ui-corner-all" onclick="term_condition_manager()">Term & Condition</button>';
    echo $block_btn = $AppUI ->createBlock('del_block', $template_btn . $term_condition_btn, 'style="text-align: left;"');
//    echo $new_quotation_block = $AppUI->createBlock('button_new', '<button class="ui-button ui-state-default ui-corner-all" onclick="new_template()"><img border="0" src="images/icons/plus.png">New Template</button>&nbsp;&nbsp;', 'style="text-align: center; float: left; margin-right : 10 px"');
    echo '<br/><br/>';
    echo '<div id="tempalte">';
        template_html();
    echo '</div>';
}

function template_html() {
    global $AppUI;
    include DP_BASE_DIR."/modules/sales/template.js.php";
    echo '<div id="list-template">';
        list_template();
    echo '</div>';
}
function list_template() {
    
    global $CTemplateManager, $AppUI;
    $template_arr = $CTemplateManager->getlist_template();
    
    $template_btn = '<button class="ui-button ui-state-default ui-corner-all" onclick="new_template()">New Template</button>&nbsp;&nbsp;';
    echo $block_btn = $AppUI ->createBlock('del_block', $template_btn, 'style="text-align: left;"');
        $dataTableRaw = new JDataTable('template_table');
        $dataTableRaw->setWidth('50%');
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
                    '<a href="?m=sales&a=vw_template&c=_view_detail_template" onclick="load_template_theme('.$template_id.'); return false;">'. $CTemplateManager->htmlChars($template['templ_name']).'</a>',
                    $template_type,
                );
                $rowIds[] = $template_id;
            }
        }

        $dataTableRaw->setDataRow($tableData, $colAttributes, $rowIds);
     
        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        $dataTableRaw->show();
        
        $delete_id = '<a class="icon-delete icon-all" onclick="delete_template(0); return false;" href="#">Delete</a>';
        $edit = '<a class="icon-edit icon-all" onclick="edit_template(0); return false;" href="#">Edit</a>';
        echo $block_del = $AppUI ->createBlock('del_block', $delete_id . $edit, 'style="text-align: left;"');
}
function _load_add_template() {
    $type = dPgetSysVal('QuotationInvoiceTemplate');
    $type_stt_dropdown = arraySelect($type, 'template_type', 'id="template_type" class="text" size="1"', '', true);
    
    $html = '<div id="form-new">';
        $html .= '<form id="add-form" method="POST">';
            $html .= '<table>
                <tr>
                    <td>Template Name: </td>
                    <td><input type="text" class="text" name="templ_name" id="templ_name" /></td>
                </tr>
                <tr>
                    <td>Template Type: </td>
                    <td>'.$type_stt_dropdown.'</td>
                </tr>
                </table>';
        $html .= '</form>';
    $html .= '</div>';
    echo $html;
}

function _do_add_template() {
    
    global $CTemplateManager, $AppUI;
    
    $template = $CTemplateManager->add_template($_POST['template_name'], $_POST['template_type']);
    if ($template) {
        echo json_encode(array('msg' => 'success', 'templ_id' => $template));
    } else {
        echo json_encode(array('msg' => 'failed'));
    }
    
}

function _view_detail_template() {
    global $AppUI;
    $template = $_POST['templ_id'];
    echo '<div id="div_0" style="overflow: hidden;">';
                _form_button($template);
    echo '</div>';
    echo  _form_menu_tab($template);
    echo '<div id="tab_detail" style="overflow:hidden"></div>';
    
}

function _form_menu_tab($template) {
    
  include DP_BASE_DIR."/modules/sales/css/quotation.css.php";
  ?>
  <div id="navcontainer">
           <ul id="navlist">
            <li><a href="#" onclick="loadTab_item(<?php echo $template; ?>); return false;">Items</a></li>
            <li><a href="#" onclick="loadtab_note(<?php echo $template; ?>); return false;">Notes</a></li>
            <li><a href="#" onclick="loadtab_term(<?php echo $template; ?>); return false;">Term and Codition</a></li>
            <li style="width: 20%"><a href="#" onclick="loadtab_sub_heading(<?php echo $template; ?>); return false;">Item table's subheading</a></li>
        </ul>
    <input type="hidden" name="template_id" id="template_id" value="<?php echo $template; ?>"/>
    </div>
<?php
}
function _form_button($template) {
    global $AppUI, $CTemplateManager;
      
      $template_name = $CTemplateManager->get_template_name($template);
        foreach ($template_name as $templ) {
            $name = $templ['templ_name'];
            if($templ['templ_type'] == 0)
                $templ_type = "Quotation";
            else
                $templ_type = "Invoice";
        }
       $h1_title = 'Template '.$templ_type.': <font color="red">'.$name.'</font>';
       
      echo $AppUI ->createBlock('div_title', '<h1 id="quotation_rev_label">'. $h1_title .'</h1>', 'style="float: left;"');
      
      $back_block = '<button id="div_details" class="ui-button ui-state-default ui-corner-all" onclick="back_list_template()">Back</button>&nbsp;';
      echo $AppUI ->createBlock('btt_block', $delete_id . $back_block . $convert_block, 'style="text-align: center; float: right"');

}
function _do_remove_template() {
    global $CTemplateManager;

    $remplate_id_arr = $_REQUEST['template_id'];

    $db_return = $CTemplateManager->remove_template($remplate_id_arr);

    if ($db_return)
        echo '{"status": "Success"}';
    else
        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
}

function _do_remove_item() {
    global $CTemplateManager;
    
    $remplate_id_arr = $_REQUEST['item_temp_id'];
    $template_id = $_POST['template_id'];

    $db_return = $CTemplateManager->remove_template_item($remplate_id_arr);

    if ($db_return)
        echo '{"status": "Success"}';
    else
        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
}
function _do_remove_term() {
    global $CTemplateManager;
    
    $remplate_id_arr = $_REQUEST['item_temp_id'];
    $term_id = $_POST['term_id'];

    $db_return = $CTemplateManager->remove_term_item($remplate_id_arr);

    if ($db_return)
        echo '{"status": "Success"}';
    else
        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
}

function add_item() {
    
    global $CTemplateManager, $AppUI;
    $msg = $CTemplateManager->insertItem($_POST['template_id'], $_POST['templ_item_name'], $_POST['templ_item_quan'], $_POST['templ_item_price'], $_POST['templ_item_discount']);
    if ($msg) 
        echo '{"status": "Success"}';
    else  
        echo '{"status": "fail"}';
}
function add_item_term_condition() {
    
    global $CTemplateManager, $AppUI;
    $msg = $CTemplateManager->insertTerm($_POST['term_and_condition'],$_POST['template_type'],$_POST['term_default']);
    if ($msg) 
        echo '{"status": "Success"}';
    else  
        echo '{"status": "fail"}';
}
function _do_add_notes() {
     global $CTemplateManager, $AppUI;
     $msg = $CTemplateManager->insertNotes($_POST['template_id'], $_POST['notes'], $_POST['note_id']);
}
function _do_add_term_condition() {
     global $CTemplateManager, $AppUI;
     $msg = $CTemplateManager->insertTermCondition($_POST['template_id'], $_POST['term_condition'], $_POST['term_id']);
}

function save_edit_item() {
     global $CTemplateManager, $AppUI;
     $msg = $CTemplateManager->save_inline_editItem($_POST['template_id'], $_POST['item_id'], $_POST['templ_item_name'], $_POST['templ_item_quan'], $_POST['templ_item_price'], $_POST['templ_item_discount']);
}
function save_item_term_condition() {
     global $CTemplateManager, $AppUI;
//     $msg = $CTemplateManager->save_inline_editTerm($_POST['term_and_condition'], $_POST['item_id']);
    $iterm_condition_id = $_REQUEST['term_condition_id'];
    $term_condition_arr = $CTemplateManager->getlist_term_condition($iterm_condition_id[0]);
//    print_r($term_condition_arr);
    $type = dPgetSysVal('QuotationInvoiceTemplate');
    $type_stt_dropdown = arraySelect($type, 'template_type', 'id="template_type" class="text" size="1"', $term_condition_arr[0]['template_type'], true);
    $checked = "";
    if($term_condition_arr[0]['term_default'] == 1)
        $checked = "checked";
    $html = '<div id="form-new">';
        $html .= '<form id="add-form" method="POST">';
            $html .= '<table>
                <tr>
                    <td>Term And Condition: </td>
                    <td><textarea rows="4" cols="40" name="term_and_condition" id="term_and_condition" >'.$CTemplateManager->htmlChars($term_condition_arr[0]['term_conttent']).'</textarea></td>

                </tr>
                <tr>
                    <td>Template Type: </td>
                    <td>'.$type_stt_dropdown.'</td>

                </tr>
                <tr>
                    <td>Default</td>
                    <td><input type="checkbox" style="padding:0px;margin:0px;" name="term_default" id="term_default" '.$checked.' /></td>
                </tr>
                </table>';
        $html .= '</form>';
    $html .= '</div>';
    echo $html;
}
function _load_edit_template() {
    global $CTemplateManager, $AppUI;
    $template_id = $_REQUEST['template_id'];
    
    $template = $CTemplateManager->get_template_db($template_id);        
    $type = dPgetSysVal('QuotationInvoiceTemplate');
    $type_stt_dropdown = arraySelect($type, 'template_type', 'id="template_type" class="text" size="1"', $template[0]['templ_type'], true);
    
    $html = '<div id="form-edit">';
        $html .= '<form id="edit-form" method="POST">';
            $html .= '<table>
                <tr>
                    <td>Template Name: </td>
                    <td><input type="text" class="text" name="templ_name" id="templ_name" value="'.$CTemplateManager->htmlChars($template[0]['templ_name']).'" /></td>
                </tr>
                <tr>
                    <td>Template Type: </td>
                    <td>'.$type_stt_dropdown.'</td>
                </tr>
                <tr>
                    <input type="hidden" name="template_id" id="template_id" value="'.$template[0]['templ_id'].'" />
                </tr>
                </table>';
        $html .= '</form>';
    $html .= '</div>';
    echo $html;
}
function _do_edit_template() {
    global $CTemplateManager, $AppUI;
    
    $template = $CTemplateManager->edit_template($_POST['template_name'], $_POST['template_type'], $_POST['template_id']);
    if ($template) {
        echo json_encode(array('msg' => 'success', 'templ_id' => $template));
    } else {
        echo json_encode(array('msg' => 'failed'));
    }
}
function template_term_condition() {
    global $CTemplateManager, $AppUI;
    include DP_BASE_DIR."/modules/sales/template.js.php";
    
    $term_condition = $CTemplateManager->getlist_term_condition();
    
        $add_id = '<a class="icon-add icon-all" id="add-new" onclick="add_term_condition();" href="#">Add Term & Condition</a>';
        echo $block_del = $AppUI ->createBlock('del_block', $add_id, 'style="text-align: left;"');
        
        echo '<div id="jdatatable_container_detail_quotation_table2" style="width: 100%; padding-bottom: 15px"><br/>';
        echo '<table cellspacing="1" cellpadding="2" style="clear: both; width: 80%;" id="detail_quotation_table" class="tbl">
                <thead>
                    <tr>
                        <th width="4%"><input type="checkbox" onclick="check_all(\'item_select_all\', \'item_check_list\');" id="item_select_all" name="item_select_all"></th>
                        <th width="6%">#</th>
                        <th width="65%">Term and Condition</th>
                        <th width="15%">Template type</th>
                        <th width="12%">Default</th>
                    </tr>
                </thead>
                <tbody>';
                
            if (isset($term_condition) && count($term_condition) > 0) {
                $i = 1;
                foreach ($term_condition as $items_item) {
                    $checked = "";
                    if($items_item['term_default']==1)
                        $checked = "checked";
                    $item_id = $items_item['term_id'];
                    if ($items_item['template_type'] == 0) {
                        $template_type = 'Quotation';
                    } else if ($items_item['template_type'] == 1) {
                        $template_type = 'Invoice';
                }
                    echo '
                        <tr valign="top" id="row_item_'. $item_id .'">
                            <td valign="top" align="center" id="">
                                <input type="checkbox" value="'. $item_id .'" name="item_check_list" id="item_check_list">
                            <td valign="top" align="center" id="stt_'. $item_id .'">'. $i .'</td>
                            <td valign="top" id="item_'. $item_id .'">'.$CTemplateManager->htmlChars($items_item['term_conttent']) .'</td>
                            <td valign="top" align="center">'.$template_type.'</td>
                            <td align="center"><input type="checkbox" value="default_'. $item_id .'" name="default_check_list" id="default_check_list_'.$item_id.'" onclick="check_termDefault('.$item_id.','.$items_item['term_default'].')" '.$checked.'></td>
                        </tr>';
                    $i++;
                }
            }
                echo '</tbody>
            </table></div><br/>';
        echo $delete_id = '<a class="icon-delete icon-all" onclick="delete_items_term('.$item_id.'); return false;" href="#">Delete</a>';
        echo $edit = '<a class="icon-edit icon-all" onclick="edit_inline_term(); return false;" href="#">Edit</a>';
}

function _load_add_term_condition() {
    $type = dPgetSysVal('QuotationInvoiceTemplate');
    $type_stt_dropdown = arraySelect($type, 'template_type', 'id="template_type" class="text" size="1"', '', true);
    
    $html = '<div id="form-new">';
        $html .= '<form id="add-form" method="POST">';
            $html .= '<table>
                <tr>
                    <td height="35">Term And Condition: </td>
                    <td><textarea rows="5" cols="40"  name="term_and_condition" id="term_and_condition" ></textarea></td>
                </tr>
                <tr>
                    <td>Template Type: </td>
                    <td>'.$type_stt_dropdown.'</td>
                </tr>
                <tr>
                    <td>Default:</td>
                    <td><input type="checkbox" style="padding:0px;margin:0px;" name="term_default" id="term_default" /></td>
                </tr>
                </table>';
        $html .= '</form>';
    $html .= '</div>';
    echo $html;
}
function _do_edit_term_condition(){
    global $CTemplateManager, $AppUI;
    $term_condition_id = $_POST['term_condition_id'];
    $term_condition_arr = $CTemplateManager->getlist_term_condition($term_condition_id[0]);
    $term_and_condition = $CTemplateManager->htmlChars($_POST['term_and_condition']);
    
    if(isset($_POST['term_and_condition']))
        $term_condition = $CTemplateManager->save_inline_editTerm($term_and_condition, $_POST['template_type'], $_POST['term_default'], $term_condition_id[0]);
    else
        
        $term_condition = $CTemplateManager->save_inline_editTerm($term_condition_arr[0]['term_conttent'],$term_condition_arr[0]['template_type'], $_POST['term_default'], $_POST['term_condition_id']);
    if ($term_condition) {
        echo json_encode(array('msg' => 'success', 'templ_id' => $term_condition));
    } else {
        echo json_encode(array('msg' => 'failed'));
    }
}
function tbl_list_template_item(){
    global $CTemplateManager;
    $item = $_POST['item'];
    $type = $_POST['type'];

    $template_item_arr = $CTemplateManager->get_db_template_item(0);
    

//        $template_item_arr = $CTemplateManager->get_db_template_item(0);
//        $i=0;
//       
//        $tableData=array(); $rowIds=array();
//        foreach ($template_item_arr as $template_item_row) {
//            $template_item_id = $template_item_row['item_temp_id'];
//            $i++;
//            $tableData[] = array(
//                $i,
//                '<span id="template_item_'.$template_item_id.'" >'.$template_item_row['item_temp_item'].'</span>',
//                '<span id="item_temp_quan_'.$template_item_id.'">'.$template_item_row['item_temp_quan'].'</span>',
//                '<span id="item_temp_price_'.$template_item_id.'">'.number_format($template_item_row['item_temp_price'], 2).'</span>',
//                '<span id="item_temp_discount_'.$template_item_id.'">'.$template_item_row['item_temp_discount'].'</span>',
//                '<span id="item_temp_amount_'.$template_item_id.'">'.number_format($template_item_row['item_temp_amount'], 2).'</span>',
//                '<a href="" id="item_temp_insert_'.$template_item_id.'" onclick="load_apply_template_item('.$item.','.$template_item_id.',\''.$type.'\');return false;">Insert</a>',
//                '<a style="margin-right:0;" class="icon-delete icon-all" onclick="remove_template_item('.$template_item_id.','.$item.',\''.$type.'\'); return false;" href="#"></a>'
//            );
//            $rowIds[] = $template_item_id; 
//        }
//        $dataTableRaw->setDataRow($tableData,$colAttributes,$rowIds);
//        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
//        echo $dataTableRaw->show();
            $dataTableRaw = new JDataTable('template_item_table');
            $dataTableRaw->setWidth('100%');
            $dataTableRaw->setHeaders(array('#','Item', 'Quantity', 'Price', 'Discount', 'Amount','','',''));

            $colAttributes = array(
                    'hidden="true" width="4%" align="center"',
                    'class="template_item_#" align="left"',
                    'class="template_item" align="left"',
                    'class="template_item_quantity" align="right" width="10%"',
                    'class="template_item_price" align="right" width="12%"',
                    'class="template_item_discount" align="right" width="10%"',
                    'class="template_item_amount" align="right" width="12%"',
                    'class="template_item_isert" align="center" width="8%"',
                    'align="right" width="3%"',
                );
            $tableData=array(); $rowIds=array();
       
        $i=0;
//        echo '<pre>';
//        print_r($template_item_arr);
        foreach ($template_item_arr as  $template_item_row) {
            $template_item_id = $template_item_row['item_temp_id'];
            $i=$template_item_row['item_temp_id'];
            $tableData[] = array(
                    '<input style="border:0; width:100%;" hidden="true" value="'.$template_item_id.'" align="center" />',
                    '<span id="template_item_a'.$template_item_id.'" >'.$template_item_row['item_temp_id'].'</span>',
                    '<span id="template_item_'.$template_item_id.'" >'.$template_item_row['item_temp_item'].'</span>',
                    '<span id="item_temp_quan_'.$template_item_id.'">'.$template_item_row['item_temp_quan'].'</span>',
                    '<span id="item_temp_price_'.$template_item_id.'">'.number_format($template_item_row['item_temp_price'], 2).'</span>',
                    '<span id="item_temp_discount_'.$template_item_id.'">'.$template_item_row['item_temp_discount'].'</span>',
                    '<span id="item_temp_amount_'.$template_item_id.'">'.number_format($template_item_row['item_temp_amount'], 2).'</span>',
                    '<a href="" id="item_temp_insert_'.$template_item_id.'" onclick="load_apply_template_item('.$item.','.$template_item_id.',\''.$type.'\');return false;">Insert</a>',
                    '<a style="margin-right:0;" class="icon-delete icon-all" onclick="remove_template_item('.$template_item_id.','.$item.',\''.$type.'\'); return false;" href="#"></a>'
                . '');
//            $i++;
            $rowIds[] = $i; 
        }
                
        $dataTableRaw->setDataRow($tableData,$colAttributes,$rowIds);
        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        echo $dataTableRaw->show();
}
function _do_getlist_template_item(){
    echo '<div id="list_template_item" style="text-align:left;">';
        tbl_list_template_item();
    echo '</div>';
}

function _do_save_subheading_template()
{
    global $CTemplateManager;
    $subheadingObj['tem_sub_heading_id'] = $_POST['subheading_id'];
    $subheadingObj['templ_id'] = $_POST['template_id'];
    $subheadingObj['sub_heading_content'] = $_POST['subheading_content'];
    
    $sub_heading_id = $CTemplateManager->add_template_sub_heading($subheadingObj);
    if($sub_heading_id>0)
    {
        echo '{"status":"success"}';
    }
}

function _do_getlist_template_subheading(){
    echo '<div id="list_template_subheading" style="text-align:left;">';
        tbl_list_template_subheading();
    echo '</div>';
}

function tbl_list_template_subheading(){
   global $CTemplateManager;
    $type = $_POST['type'];
    $dataTableRaw = new JDataTable('template_item_table');
        $dataTableRaw->setWidth('100%');
        $dataTableRaw->setHeaders(array('#', 'Description', 'Action'));
        
        $colAttributes = array(
                ' width="4%" align="center"',
                'class="template_item" align="left"',
                'class="template_item_quantity" align="center" width="15%"',
            );
        $template_arr = $CTemplateManager->get_db_sub_heading_template(false,$type);
        $i=0;
       
        $tableData=array(); $rowIds=array();
        foreach ($template_arr as $template_item_row) {
            $i++;
            $template_item_id = $template_item_row['tem_sub_heading_id'];
            $tableData[] = array(
                $i,
                '<span id="template_item_'.$template_item_id.'" >'.nl2br($template_item_row['sub_heading_content']).'</span>',
                '<a href="" id="item_temp_insert_'.$template_item_id.'" onclick="load_apply_template_subHeading('.$template_item_id.',\''.$type.'\');return false;">Insert</a>
                <a style="margin-right:0;" class="icon-delete icon-all" onclick="remove_template_subheading('.$template_item_id.',\'subheading\'); return false;" href=""></a>'
            );
            $rowIds[] = $template_item_id;   
        }
        $dataTableRaw->setDataRow($tableData,$colAttributes,$rowIds);
        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        echo $dataTableRaw->show();
}

function _do_getlist_template_subject(){
    echo '<div id="list_template_subject" style="text-align:left;">';
        tbl_list_template_subject();
    echo '</div>';
}

function tbl_list_template_subject(){
    global $CTemplateManager;
    $type = $_POST['type'];
    $action = $_POST['action'];
    $dataTableRaw = new JDataTable('template_item_table');
        $dataTableRaw->setWidth('100%');
        $dataTableRaw->setHeaders(array('#', 'Description', 'Action'));
        
        $colAttributes = array(
                ' width="4%" align="center"',
                'class="template_item" align="left"',
                'class="template_item_quantity" align="center" width="15%"',
            );
        $template_arr = $CTemplateManager->get_db_sub_heading_template(false,$type);
        $i=0;
       
        $tableData=array(); $rowIds=array();
        foreach ($template_arr as $template_item_row) {
            $i++;
            $template_item_id = $template_item_row['tem_sub_heading_id'];
            $tableData[] = array(
                $i,
                '<span id="template_item_'.$template_item_id.'" >'.nl2br($template_item_row['sub_heading_content']).'</span>',
                '<a href="" id="item_temp_insert_'.$template_item_id.'" onclick="load_apply_template_subject('.$template_item_id.',\''.$type.'\',\''.$action.'\');return false;">Insert</a>
                <a style="margin-right:0;" class="icon-delete icon-all" onclick="remove_template_subject('.$template_item_id.',\'subject\'); return false;" href="#"></a>'
            );
            $rowIds[] = $template_item_id;   
        }
        $dataTableRaw->setDataRow($tableData,$colAttributes,$rowIds);
        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        echo $dataTableRaw->show();
}

function _do_remove_template_subheading()
{
    global $CTemplateManager;
    
    $reault = $CTemplateManager->remove_template_subheading($_POST['template_subheading_id']);
    if($reault)
        echo '{"status":"success"}';
    else
        echo '{"status":"fail"}';
}
?>

<script type="text/javascript">
    var template_id = $('#template_id').val();
    $(document).ready(function() {
       loadTab_item(template_id); 
    });
</script>