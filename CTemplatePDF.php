<?php

if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CTemplatePDF extends CDpObject {

    public $template_pdf_id;
    public $template_default;

    public function CTemplatePDF() {
            $this-> CDpObject("sales_template_pdf", "template_pdf_id");
    }
    
    public function update_template_pdf($template_pdf_id, $template_default) {
        $CTemplatePDF = new CTemplatePDF();
        $CTemplatePDF->load($template_pdf_id);
        $CTemplatePDF->template_default = $template_default;
        return $CTemplatePDF->store();
    }
    
    public function get_template_pdf($template_pdf_id=false)
    {
        $q=new DBQuery();
        $q->addTable('sales_template_pdf');
        $q->addQuery('*');
        if($template_pdf_id)
            $q->addWhere('template_pdf_id='.intval($template_pdf_id));
        return $q->loadList();
    }
    
    public function update_template_pdf_footer($payment_id, $footer_text) {
        $CTemplatePDF = new CTemplatePDF();
        $CTemplatePDF->load($payment_id);
        $CTemplatePDF->footer_text_invoice = $footer_text;
        return $CTemplatePDF->store();
    }

}

?>
