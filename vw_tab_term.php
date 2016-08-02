<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once 'CSalesManager.php';
require_once (DP_BASE_DIR."/modules/sales/CTemplateManager.php");

$CSalesManager = new CSalesManager();
$CTemplateManager = new CTemplateManage();

global  $AppUI, $m;

function __default() {
    $template_id = $_REQUEST['template_id'];
    form_term_template($template_id);
}

function form_term_template($template_id) {
    global $AppUI, $CTemplateManager;

    include DP_BASE_DIR."/modules/sales/template.js.php";
    $insert_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="insert_term_condition('.$template_id.'); return false;">Insert Term & Condition</button>';

    $term_condition = $CTemplateManager->getdb_term_condition($template_id);
    $term_content = $term_condition[0]['note_temp_content'];
    $textarea1 = '<textarea id="term_condition" class="textArea" style="width: 500; height: 100;" name="term_condition" value="'.$term_content.'">'.$term_content.'</textarea>';

    if (count($term_condition) > 0) {
        $term_id = $term_condition[0]['term_temp_id'];
    } else {
        $term_id = 0;
    }
    $terms_block = $AppUI ->createBlock('div_terms', 'Terms and Conditions: <br/>'.$insert_block .'<br/>'. $textarea1, 'style="float: left;width:100%;"');
    
    $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_term_condition('. $template_id .', '.$term_id.'); return false;">Save Option</button>&nbsp';

    echo '<div id="div_5">' . $terms_block .'</div>';
    echo $AppUI ->createBlock('btt_block', $save_block , 'style="text-align: center; float: left;"');
    echo '<br/>';
}

function _load_term_condition() {
    global $CTemplateManager;
    $template_id = $_GET['template_id'];
    $template_arr = $CTemplateManager->get_template_name($template_id);
//    echo $template_arr[0]['templ_type'];
    if(isset($_GET['template_id']))
        $list_term = $CTemplateManager->getdb_term_condition_By_type($template_arr[0]['templ_type']);
    else {
        $list_term = $CTemplateManager->getdb_term_condition_By_type($_GET['template_term_id']);
    }
        
    //print_r($list_term);
    $html = '<div>';
    $html .='<table id="list_term" class="tbl" cellspacing="1" cellpadding="2" style="clear: both; width: 100%">
            <tr>
                <th>#</th>
                <th>Term & Condition</th>
                <th class="check_option"></th>
            </tr>';
    $i = 1;
    foreach ($list_term as $term) {
        $html .= '<tr>
                    <td>'.$i.'</td>
                    <td>'.$term['term_conttent'].'</td>
                    <td><input type="checkbox" id="check_template" name="check_template[]" value="'.$term['term_conttent'].'" /></td>
                </tr>';
        $i++;
    }
    $html .= '</table></div>';
    echo $html;
}

?>
