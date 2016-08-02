<?php


if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CTemplate extends CDpObject {
    public $templ_id;
    public $templ_name;
    public $templ_type;
    public $user_id;
    
    public function CTemplate() {
        $this->CDpObject('sales_template', 'templ_id');
    }
}
?>
