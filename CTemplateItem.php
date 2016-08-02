<?php


if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

class CTemplateItem extends CDpObject {
    public $item_temp_id;
    public $templ_id;
    public $item_temp_item;
    public $item_temp_quan;
    public $item_temp_price;
    public $item_temp_discount;
    public $item_temp_amount;
    
    public function CTemplateItem() {
        $this->CDpObject('sale_template_items', 'item_temp_id');
    }
}

?>
