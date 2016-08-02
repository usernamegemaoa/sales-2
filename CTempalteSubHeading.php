<?php
if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CTempalteSubHeading extends CDpObject {

    public $tem_sub_heading_id;
    public $templ_id;
    public $sub_heading_content;
    public $type;

    public function CTempalteSubHeading() {
        $this-> CDpObject("sales_template_sub_heading", "tem_sub_heading_id");
    }
    
    public function addTemplateSubheading($obj)
    {
        $CTemplate = new CTempalteSubHeading();
        $CTemplate->bind($obj);
        $CTemplate->store();
        return $CTemplate->tem_sub_heading_id;
    }

}

?>
