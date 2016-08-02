<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
require_once 'CSalesManager.php';
require_once (DP_BASE_DIR."/modules/sales/CTemplateManager.php");

$CSalesManager = new CSalesManager();
$CTemplateManager = new CTemplateManage();


function __default() {
    $template_id = $_REQUEST['template_id'];
    show_html_table($template_id);
}
function show_html_table($template_id) {
    
    include DP_BASE_DIR."/modules/sales/template.js.php";
    
    global $AppUI, $CSalesManager, $CTemplateManager;

    $tempalte_item_arr = array();
        
        $tempalte_item_arr = $CTemplateManager->get_db_template_item($template_id);
                
        $add_id = '<a class="icon-add icon-all" id="add-new" onclick="add_template_item('.$template_id.');" href="#">Add item</a>';
        echo '<div id="jdatatable_container_detail_quotation_table2" style="width: 100%; padding-bottom: 15px"><br/>';
        echo $block_del = $AppUI ->createBlock('del_block', $add_id, 'style="text-align: left;"');
        echo'<input type="hidden" value="'. $template_id .'" name="template_id_'.$template_id.'" id="template_id_'.$template_id.'">';
        echo '<table cellspacing="1" cellpadding="2" style="clear: both; width: 100%" id="detail_quotation_table" class="tbl">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="check_all(\'item_select_all\', \'item_check_list\');" id="item_select_all" name="item_select_all"></th>
                        <th>#</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>';
                
            if (isset($tempalte_item_arr) && count($tempalte_item_arr) > 0) {
                $i = 1;
                foreach ($tempalte_item_arr as $items_item) {
                    $item_id = $items_item['item_temp_id'];
                    echo '
                        <tr valign="top" id="row_item_'. $item_id .'">
                            <td valign="top" width="5%" align="center" id="">
                                <input type="checkbox" value="'. $item_id .'" name="item_check_list" id="item_check_list">
                                <a class="icon-edit icon-all" onclick="edit_inline_item('. $item_id .'); return false;" href="#" title="Inline Edit" style="margin-right:0px"></a></td>
                            <td valign="top" width="4%" align="center" id="stt_'. $item_id .'">'. $i .'</td>
                            <td valign="top" width="50%">
                                '. nl2br($items_item['item_temp_item']) .'
                                <input id="item_'. $item_id .'" type="hidden" value="'.$items_item["item_temp_item"].'" />
                            </td>
                            <td valign="top" width="10%" align="right" id="quantity_'. $item_id .'">'. $items_item['item_temp_quan'] .'</td>
                            <td valign="top" width="10%" align="right" id="price_'. $item_id .'">'. $items_item['item_temp_price'] .'</td>
                            <td valign="top" width="10%" align="right" id="discount_'. $item_id .'">'. $items_item['item_temp_discount'] .'%</td>
                            <td valign="top" width="10%" align="right" id="total_'. $item_id .'">'. '$'. $CSalesManager->calculate_total_item($items_item['item_temp_price'], $items_item['item_temp_quan'], $items_item['item_temp_discount']) .'</td>
                        </tr>';
                    $i++;
                }
            }
                echo '</tbody>
            </table></div><br/>';
        
        echo $delete_id = '<a class="icon-delete icon-all" onclick="delete_items_item('.$template_id.'); return false;" href="#">Delete</a>';
    
}

?>
