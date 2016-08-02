<?php
require_once 'CDpObject.php';

/**
 * XXX detailed description
 *
 * @author    XXX
 * @version   XXX
 * @copyright XXX
 */
class CHeaderDefault extends CDpObject {
    // Attributes
    /**
     * XXX
     *
     * @var    tinyint
     * @access public
     */
    public $header_id;

    /**
     * XXX
     *
     * @var    int(11)
     * @access public
     */
    public $user_id;

    /**
     * XXX
     *
     * @var    text
     * @access public
     */
    public $header_contents;

    // Associations
    // Operations
    /**
     * XXX
     * 
     * @return Object XXX
     * @access public
     */
    public function CHeaderDefault()
     {
        trigger_error('Not Implemented!', E_USER_WARNING);
    }

    /**
     * XXX
     * 
     * @param  CHeaderDefault $headerObj XXX
     * @return boolean XXX
     * @access public
     */
    public function add_header(CHeaderDefault $headerObj)
     {
        trigger_error('Not Implemented!', E_USER_WARNING);
    }

    /**
     * XXX
     * 
     * @param  int $header_id XXX
     * @param  string $value XXX
     * @return boolean XXX
     * @access public
     */
    public function update_header($header_id, $value)
     {
        trigger_error('Not Implemented!', E_USER_WARNING);
    }

    /**
     * XXX
     * 
     * @param  int $header_id XXX
     * @return boolean XXX
     * @access public
     */
    public function remove_header($header_id)
     {
        trigger_error('Not Implemented!', E_USER_WARNING);
    }

}

?>
