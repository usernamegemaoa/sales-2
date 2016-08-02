<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class CTermCondition extends CDpObject {
    public $term_id;
    public $term_conttent;
    public $template_type;
    public $term_default;
    public function CTermCondition() {
        $this->CDpObject('sale_term_condition', 'term_id');
    }
}
?>
