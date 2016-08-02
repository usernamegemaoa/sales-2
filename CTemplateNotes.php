<?php


if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

class CTemplateNotes extends CDpObject {
    public $note_temp_id;
    public $templ_id;
    public $note_temp_content;

    public function CTemplateNotes() {
        $this->CDpObject('sale_template_note', 'note_temp_id');
    }
}
?>
