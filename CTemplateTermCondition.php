<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
class CTemplateTermCondition extends CDpObject {
    public $term_temp_id;
    public $templ_id;
    public $note_temp_content;

    public function CTemplateTermCondition() {
        $this->CDpObject('sale_template_term_condition', 'term_temp_id');
    }
}

?>
