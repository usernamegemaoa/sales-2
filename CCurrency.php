<?php

class CCurrency extends CDpObject {

    public $currency_id;
    public $user_id;
    public $currency_name;
    public $currency_symbol;

    public function CCurrency() {
        $this-> CDpObject("sales_invoice", "invoice_id");
    }

    /**
     * XXX
     * 
     * @param  CCurrency $currencyObj XXX
     * @return boolean XXX
     * @access public
     */
    public function add_currency(CCurrency $currencyObj)
     {
        trigger_error('Not Implemented!', E_USER_WARNING);
    }

    /**
     * XXX
     * 
     * @param  int $currency_id XXX
     * @param  string $currency_field XXX
     * @param  string $value XXX
     * @return boolean XXX
     * @access public
     */
    public function update_currency($currency_id, $currency_field, $value)
     {
        trigger_error('Not Implemented!', E_USER_WARNING);
    }

    /**
     * XXX
     * 
     * @param  int $currency_id XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_currency($currency_id)
     {
        trigger_error('Not Implemented!', E_USER_WARNING);
    }

}

?>
