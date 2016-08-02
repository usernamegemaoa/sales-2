<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once 'CTemplate.php';
require_once 'CTemplateItem.php';
require_once 'CTemplateNotes.php';
require_once 'CTemplateTermCondition.php';
require_once 'CTermCondition.php';
require_once 'CTempalteSubHeading.php';

class CTemplateManage {
    
    function getlist_template() {
        $q = new DBQuery();
        $q->addTable('sales_template');
        $q->addQuery('*');
        return $q->loadList();
    }
    function getlist_template_for_Quotation() {
        $q = new DBQuery();
        $q->addTable('sales_template');
        $q->addQuery('*');
        $q->addWhere('templ_type = 0');
        return $q->loadList();
    }
    function getlist_template_for_Invoice() {
        $q = new DBQuery();
        $q->addTable('sales_template');
        $q->addQuery('*');
        $q->addWhere('templ_type = 1');
        return $q->loadList();
    }
    
    function insertItem($template_id, $item_name, $quantity, $price, $discount, $amount=0) {
        $obj = new CTemplateItem();
        $obj->item_temp_id = 0;
        $obj->templ_id = $template_id;
        $obj->item_temp_item = $item_name;
        $obj->item_temp_quan = $quantity;
        $obj->item_temp_price = $price;
        $obj->item_temp_discount = $discount;
        $obj->item_temp_amount = $amount;
        $obj->store();
        return $obj->item_temp_id;
        
    }
    
    function add_template($name, $type) {
        global $AppUI;
        
        $CTemplate = new CTemplate();
        $CTemplate->templ_id = 0;
        $CTemplate->templ_name = $name;
        $CTemplate->templ_type = $type;
        $CTemplate->user_id = $AppUI->user_id;
        $CTemplate->store();
        return $CTemplate->templ_id;
    }
    function insertTerm($name,$type,$default) {
        global $AppUI;
        
        $CTerm = new CTermCondition();
        $CTerm->term_id = 0;
        $CTerm->term_conttent = $name;
        $CTerm->template_type = $type;
        $CTerm->term_default = $default;
        $CTerm->store();
        return $CTerm->term_id;
    }
    function edit_template($name, $type, $id) {
        global $AppUI;
        
                $CTemplate = new CTemplate();
                $CTemplate->load($id);
                $CTemplate->templ_name = $name;
                $CTemplate->templ_type = $type;
                $CTemplate->user_id = $AppUI->user_id;
                return $CTemplate->store();
        
    }
    function insertNotes($template_id, $notes, $note_id = null) {
        global $AppUI;
        if(!$note_id) {
            $CTemplate_note = new CTemplateNotes();
            $CTemplate_note->note_temp_id = 0;
            $CTemplate_note->templ_id = $template_id;
            $CTemplate_note->note_temp_content = $notes;
            $CTemplate_note->store();
            return $CTemplate_note->note_temp_id;
        } else {
            $CTemplate_note = new CTemplateNotes();
            $CTemplate_note->load($note_id);
            $CTemplate_note->note_temp_content = $notes;
            return $CTemplate_note->store();
        }
    }
    function insertTermCondition($template_id, $term_content, $term_id = null) {
        global $AppUI;
        if(!$term_id) {
            $CTemplate_term = new CTemplateTermCondition();
            $CTemplate_term->term_temp_id = 0;
            $CTemplate_term->templ_id = $template_id;
            $CTemplate_term->note_temp_content = $term_content;
            $CTemplate_term->store();
            return $CTemplate_term->term_temp_id;
        } else {
            $CTemplate_term = new CTemplateTermCondition();
            $CTemplate_term->load($term_id);
            $CTemplate_term->note_temp_content = $term_content;
            return $CTemplate_term->store();
        }
    }
    function save_inline_editItem($template_id, $item_id, $item_name, $item_quantity, $item_price, $item_discount) {
        $obj = new CTemplateItem();
        
        $obj->load($item_id);
        $obj->templ_id = $template_id;
        $obj->item_temp_item = $item_name;
        $obj->item_temp_quan = $item_quantity;
        $obj->item_temp_price = $item_price;
        $obj->item_temp_discount = $item_discount;
        $obj->item_temp_amount = 0;
        return $obj->store();
    }
    function save_inline_editTerm($template_id, $type, $default, $item_id ) {
        $obj = new CTermCondition();
        
        $obj->load($item_id);
        $obj->term_conttent = $template_id;
        $obj->template_type = $type;
        $obj->term_default = $default;
        return $obj->store();
    }
    function get_template_name($template) {
        $q = new DBQuery();
        $q->addTable('sales_template');
        $q->addQuery('*');
        $q->addWhere('sales_template.templ_id ='.intval($template));
        return $q->loadList();
    }
    function remove_template($template_id_arr) {
        if (($count = count($template_id_arr)) > 0) {
            $where = 'templ_id = '. $template_id_arr[0];
            for ($i = 1; $i < $count; $i++) {
                $where .= ' OR templ_id = '. $template_id_arr[$i];
            }
        }
        
        if ($where) {
            $sql1 = "delete from sale_template_items where ".$where; // xoa bang quotation
            $sql2 = "delete from sale_template_note where ".$where; // xoa bang quotation_revision
            $sql2 = "delete from sale_template_term_condition where ".$where; // xoa bang quotation_revision
            $sql3 = "delete from sales_template where ".$where; // xoa bang quotation_item
            if(db_exec($sql1) && db_exec($sql2) && db_exec($sql3)) {
                return true;
            } else
                return false;
        } else
            return false;
    }
    function get_db_template_item($template_id) {
        $q = new DBQuery();
        $q->addTable('sale_template_items');
        $q->addQuery('*');
        if($template_id)
            $q->addWhere('templ_id = '.$template_id);
        $q->addOrder('item_temp_id ASC');
        return $q->loadList();
    }
    function remove_template_item($template_id_arr) {
        if (($count = count($template_id_arr)) > 0) {
            $where = 'item_temp_id = '. $template_id_arr[0];
            for ($i = 1; $i < $count; $i++) {
                $where .= ' OR item_temp_id = '. $template_id_arr[$i];
            }
        }
        
        if ($where) {
            $sql1 = "delete from sale_template_items where ".$where; // xoa bang quotation
            if(db_exec($sql1)) {
                return true;
            } else
                return false;
        } else
            return false;
    }
    function remove_term_item($template_id_arr) {
        if (($count = count($template_id_arr)) > 0) {
            $where = '	term_id = '. $template_id_arr[0];
            for ($i = 1; $i < $count; $i++) {
                $where .= ' OR 	term_id = '. $template_id_arr[$i];
            }
        }
        
        if ($where) {
            $sql1 = "delete from sale_term_condition where ".$where; // xoa bang quotation
            if(db_exec($sql1)) {
                return true;
            } else
                return false;
        } else
            return false;
    }
    function get_db_note_template($template_id) {
        $q = new DBQuery();
        $q->addTable('sale_template_note');
        $q->addQuery('*');
        $q->addWhere('templ_id='.$template_id);
        return $q->loadList();
    }
    function getdb_term_condition($template_id) {
        $q = new DBQuery();
        $q->addTable('sale_template_term_condition');
        $q->addQuery('*');
        $q->addWhere('templ_id='.intval($template_id));
        return $q->loadList();
    }
    function get_template_db($template_arr) {
              if ($count = count($template_arr) > 0) {
                  $where = 'templ_id = '. $template_arr[0];
                    for ($i = 1; $i < $count; $i++) {
                        $where .= ' OR templ_id = '. $template_arr[$i];
                    }
                }
                if ($where) {
                    $q = new DBQuery();
                    $q->addTable('sales_template');
                    $q->addQuery('*');
                    $q->addWhere($where);
                    $rows = $q->loadList();
                    return $rows;
                } else {
                    return FALSE;
                }
        }
        function getlist_term_condition($term_condition_id) {
            $q = new DBQuery();
            $q->addTable('sale_term_condition');
            $q->addQuery('*');
            if($term_condition_id)
                $q->addWhere ('term_id='.  intval($term_condition_id));
            $q->addOrder('template_type DESC');
            return $q->loadList();
            
        }
// Ham chuyen the HTML ve nguyen dang cua no de hien thi ra man hinh (the <br/> khong bi chuyen doi >
        function htmlChars($str){
            $str=  preg_replace("/<(?!br)/", '&lt;', $str);
            $str=  preg_replace("/'/", '&#039;', $str);
            $str=  preg_replace('/"/', '&quot;', $str);
            return $str;
        }
    function getdb_term_condition_By_type($template_type) {
            $q = new DBQuery();
            $q->addTable('sale_term_condition');
            $q->addQuery('*');
            if($template_type==1)
                $q->addWhere ('template_type='.  intval($template_type));
            else if($template_type==0)
                $q->addWhere ('template_type=0');
            return $q->loadList();
    }
    function is_exits_template_item($item_description,$template_id=false){
        $q=new DBQuery();
        $q->addTable('sale_template_items');
        $q->addWhere('item_temp_item="'.$item_description.'"');
        if($template_id)
            $q->addWhere ('templ_id='.intval($template_id));
        $count = count($q->loadList());
        if($count>0)
            return true;
        return false;
        
    }
    
    public function add_template_sub_heading($subHeadingObj)
    {
        $CTemplateSubHeading = new CTempalteSubHeading();
        $CTemplateSubHeading->bind($subHeadingObj);
        $CTemplateSubHeading->store();
        return $CTemplateSubHeading->tem_sub_heading_id;
    }
    function get_db_sub_heading_template($template_id=false,$type=false) {
        $q = new DBQuery();
        $q->addTable('sales_template_sub_heading');
        $q->addQuery('*');
        if($template_id)
            $q->addWhere('templ_id='.$template_id);
        if($type)
            $q->addWhere('type="'.$type.'"');
        return $q->loadList();
    }
    
    function is_tempalte_subheading($content,$type)
    {
        $q = new DBQuery();
        $q->addTable('sales_template_sub_heading');
        $q->addQuery('*');
        $q->addWhere('sub_heading_content="'.$content.'"');
        $q->addWhere('type="'.$type.'"');
        $rows = $q->loadList();
        
        $count = count($rows);
        
        if($count>0)
            return true;
        return false;
    }
    
    function remove_template_subheading($template_id) {
        $sql1 = "delete from sales_template_sub_heading where tem_sub_heading_id=".$template_id; // xoa bang quotation
        if(db_exec($sql1)) {
            return true;
        } else
            return false;
    }
}
?>
