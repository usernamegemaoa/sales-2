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
    form_note_template($template_id);
}

function form_note_template($template_id) {
    global $AppUI, $CTemplateManager;
    
    $notes = $CTemplateManager->get_db_note_template($template_id);
    $textarea = '<textarea id="template_note" class="textArea" style="width: 500; height: 100;" name="template_note">'.$notes[0]['note_temp_content'].'</textarea>';

    if (count($notes) > 0) {
        $note_id = $notes[0]['note_temp_id'];
    } else {
        $note_id = 0;
    }
    $note_block = $AppUI ->createBlock('div_notes', 'Notes: <br/>'. $textarea, 'style="float: left;width:100%;"');
    
    $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_notes('. $template_id .', '.$note_id.'); return false;">Save Option</button>&nbsp';

    echo '<div id="div_5">' . $note_block .'</div>';
    echo $AppUI ->createBlock('btt_block', $save_block , 'style="text-align: center; float: left"');
    echo '</br>';
}

?>
