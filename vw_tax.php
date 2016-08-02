<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/sales/CTax.php");

$CTax = new CTax();

function __default() {
    
    if (_check_view_config) {
        echo '<div id="div_tax">';
                show_html_table();
        echo '</div>';
    }
    
}

function show_html_table() {
    if (_check_view_config) {
        require_once (DP_BASE_DIR."/modules/sales/tax.js.php");

        global $AppUI; global $CTax; global $AppUI;

        $dataTableRaw = new JDataTable('tax_table');
        $dataTableRaw->setWidth('60%');
        $dataTableRaw->setHeaders(array(
            '<input type="checkbox">',
            'Name',
            'Rate',
            'Added by'
            ));

        $colAttributes = array(
            'class="checkbox" width="5%" align="center"',
            'class="edit_tax_name" align="center" width="10%"',
            'class="edit_tax_rate" width="20%"',
            'width="20%"',
        );

        $tableData = array();
        
        $tax_arr = $CTax->list_tax();

        if (count($tax_arr) > 0) {
            foreach ($tax_arr as $tax) {
                $tableData[]= array(
                    '<input type="checkbox">',
                    $tax['tax_name'],
                    $tax['tax_rate'],
                    dPgetUsername($tax['user_id'])
                );
            }
        }
                $tableData[]= array(
                    '',
                    '<input type="text" name="tax_name" id="tax_name" />',
                    '<input type="text" name="tax_rate" id="tax_rate" />',
                    '<input type="hidden" name="user_id" id="user_id" value="'.$AppUI->user_id.'" /><img border="0" style="cursor: pointer" onclick="save_tax();" src="images/icons/save.png">'
                );


        $dataTableRaw->setDataRow($tableData, $colAttributes);
        $dataTableRaw->setJEditable('edit_tax_name', '?m=sales&a=vw_tax&c=update_field&suppressHeaders=true');
        $dataTableRaw->setJEditable('edit_tax_rate', '?m=sales&a=vw_tax&c=update_field&suppressHeaders=true');
        
        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        $dataTableRaw->show();

        $delete_id = '<a onclick="" href="#"><img style="border: medium none;" src="modules/leave/images/delete.png"> delete </a>';
        echo $block_del = $AppUI ->createBlock('del_block', $delete_id, '');
    }

}

function do_add_tax() {
    if (_check_view_config) {
        global $CTax;

        $tax_id = $CTax->add_tax($_POST);

        if ($tax_id > 0) {
            echo "success";
        } else {
            echo "Failed, message: '" . $tax_id . "'";
        }
    }
}

function update_field() {
    
}

function _check_view_config() {
    
    global $AppUI;

    $UserType = dPgetSysVal('UserType');

    if ($UserType[$AppUI->user_type] == 'Administrator')
        return true;
    else
        return false;
        
}
function popup_add_tax(){
    global $AppUI;
    echo '<table style="margin:auto;">
            <tr>
                <td width="50">Name:</td>
                <td><input class="txt" type="text" size="30" value="GST" id="tax_name" name="tax_name"></td>
            </tr>
            <tr>
                <td width="50">Rate:</td>
                <td><input class="txt" type="text" size="30" id="tax_value" value="" name="tax_value"></td>
            </tr>
            <tr align="center">
                <td colspan="2">
                    <input type="button" name="tax_save" id="tax_save" onclick="save_add_tax(); return false;" value="Save" />&nbsp;&nbsp;
                    <input type="button" name="reset" value="Cancel" />
                </td>
            </tr>
    </table>';
}

function load_tax_option(){
    global $AppUI;
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();
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
    echo $tax_option;
}

?>
