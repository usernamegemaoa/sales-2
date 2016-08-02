<?php


if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/sales/CSalesManager.php");
require_once (DP_BASE_DIR."/modules/sales/CTemplateManager.php");

$CSalesManager = new CSalesManager();
$CTemplateManager = new CTemplateManage();

global  $AppUI, $m;

function __default() {
    $template_id = $_REQUEST['template_id'];
    form_subheading_template($template_id);
}

function form_subheading_template($template_id) {
    global $AppUI, $CTemplateManager;
    
    $subheading = $CTemplateManager->get_db_sub_heading_template($template_id);
    $textarea = '<textarea id="template_subheading" class="textArea" style="width: 500; height: 100;" name="template_note">'.$subheading[0]['sub_heading_content'].'</textarea>';

    if (count($subheading) > 0) {
        $subheading_id = $subheading[0]['tem_sub_heading_id'];
    } else {
        $subheading_id = 0;
    }
    $subheading_block = $AppUI ->createBlock('div_sudheading', "Item table's subheading: <br/>". $textarea, 'style="float: left;width:100%;"');
    
    $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_subheading('. $template_id .', '.$subheading_id.'); return false;">Save</button>&nbsp';

    echo '<div id="div_5">' . $subheading_block .'</div>';
    echo $AppUI ->createBlock('btt_block', $save_block , 'style="text-align: center; float: left"');
    echo '</br>';
}
?>