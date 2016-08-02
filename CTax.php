<?php

if (!defined('DP_BASE_DIR')){
		die('You should not access this file directly.');
}

class CTax extends CDpObject {

    public $tax_id;
    public $user_id;
    public $tax_name;
    public $tax_rate;

    public function CTax() {
        $this-> CDpObject("sales_tax", "tax_id");
    }

    /**
     * XXX
     * 
     * @return array XXX
     * @access public
     */
    public function list_tax() {
            //$CTax = new CTax();
            return CTax::loadAll();
    }

    /**
     * XXX
     *
     * @param  CTax $taxObj XXX
     * @return boolean XXX
     * @access public
     */
    public function add_tax($taxObj) {
            $CTax = new CTax();
            $CTax->bind($taxObj);
            $CTax->store($taxObj);
            return $CTax->tax_id;
    }

    /**
     * XXX
     * 
     * @param  int $tax_id XXX
     * @param  string $tax_field XXX
     * @param  string $value XXX
     * @return boolean XXX
     * @access public
     */
    public function update_tax($tax_id, $tax_field, $value) {
        $CTax = new CTax();
        $CTax->load($tax_id);
        $CTax->$tax_field = $value;
        return $CTax->store();
    }

    /**
     * XXX
     * 
     * @param  int $tax_id XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_tax($tax_id_arr) {
        if (($count = count($tax_id_arr)) > 0) {
            $where = 'tax_id = '. $tax_id_arr[0];
            for ($i = 1; $i < $count; $i++) {
                $where .= ' OR tax_id = '. $tax_id_arr[$i];
            }
        }
        if ($where) {
            $sql = "delete from sales_tax where ".$where;
            if(db_exec($sql))
                return true;
            else
                return false;
        } else
            return false;
    }
    public static function get_tax($tax_id){
        $q=new DBQuery();
        $q->addTable('sales_tax');
        $q->addQuery('*');
        $q->addWhere('tax_id='.intval($tax_id));
        return $q->loadList();
    }

}

?>
