<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/sales/CTemplateManager.php");
include DP_BASE_DIR."/modules/sales/quotation.js.php";
include DP_BASE_DIR."/modules/sales/template.js.php";
$CTemplateManager = new CTemplateManage();
    global $AppUI;
    $template_btn = '<button class="ui-button ui-state-default ui-corner-all" onclick="back_list_template(); return false;">Template</button>&nbsp;&nbsp;';
    $term_condition_btn = '<button class="ui-button ui-state-default ui-corner-all" onclick="term_condition_manager()">Term & Condition</button>';
    echo $block_btn = $AppUI ->createBlock('del_block', $template_btn . $term_condition_btn, 'style="text-align: left;"');
//    echo $new_quotation_block = $AppUI->createBlock('button_new', '<button class="ui-button ui-state-default ui-corner-all" onclick="new_template()"><img border="0" src="images/icons/plus.png">New Template</button>&nbsp;&nbsp;', 'style="text-align: center; float: left; margin-right : 10 px"');
    echo '<br/><br/>';
    echo '<div id="tempalte">';
    global $AppUI;
        include DP_BASE_DIR."/modules/sales/template.js.php";
        echo '<div id="list-template">';
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
        echo '</div>';
    echo '</div>';
?>
