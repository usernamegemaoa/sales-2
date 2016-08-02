<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR.'/modules/po/CNextNumber.php');
$CNextNumber = new CNextNumber();
define(FORMAT_DATE_DEFAULT, 'd/m/Y');

//include DP_BASE_DIR."/modules/sales/CInvoiceManager.php";
//include DP_BASE_DIR."/modules/sales/CQuotationManager.php";
//require_once DP_BASE_DIR."/modules/sales/CPaymentManager.php";
require 'CSalesAttention.php';
class CSalesManager {

    
    /**
     * XXX
     *
     * @param  int $customer_id XXX
     * @return string XXX
     * @access public
     */
    public function get_customer_email($invoice_id) {

            $q = new DBQuery();
            $q->addTable('sales_invoice');
            $q->addQuery('customer_id');
            $q->addWhere('invoice_id = '.intval($invoice_id));
            $return = $q->loadList();


            $company_id = $return[0]['customer_id'];
            //print_r($company_id);
            //asprint_r($return);
            
            $rows = CSalesManager::get_address_by_company($company_id);
            if ($rows)
                return $rows['address_email'];
            else
                return 'tinhdx.it@gmail.com';
    }

    //Lay email cua nguoi gui khi thuc hien gui email trong Quotation (From) - anhnn    
    public function get_customer_email_quo($quotation_id) {

        $q = new DBQuery;
        $q->addTable('sales_quotation');
        $q->addQuery('quotation_sale_person_email');
        $q->addWhere('quotation_id = '. intval($quotation_id));
        $rows = $q->loadList();
        if ($rows)
            return $rows[0];
        else
            return null;
    }

//Lay email cua nguoi gui khi thuc hien gui email trong Invoice (From) - anhnn
    public function get_customer_email_invoice($invoice_id) {

        $q = new DBQuery;
        $q->addTable('sales_invoice');
        $q->addQuery('invoice_sale_person_email');
        $q->addWhere('invoice_id = '. intval($invoice_id));
        $rows = $q->loadList();
        if ($rows)
            return $rows[0];
        else
            return null;
    }

//Lay email cua nguoi nhan khi thuc hien gui email (To) - anhnn    
    public function get_attention_email($attention_id) {
            $q = new DBQuery();
            $q->addTable('contacts', 'tbl1');
            $q->addQuery('tbl1.*');
            $q->addWhere('tbl1.contact_id = '. intval($attention_id));
            $rows = $q->loadList();
        if ($rows)
            return $rows[0];
        else
            return null;
    }
    
    /**
     * XXX
     *
     * @param  float $price XXX
     * @param  int $quantity XXX
     * @param  float $discount XXX
     * @return float XXX
     * @access public
     */
    public static function calculate_total_item($price, $quantity, $discount) {
        //setlocale(LC_MONETARY, 'en_US'); // Cai dat format tien te moi
        $amount_item = (($price * $quantity) - ($price * $quantity * ($discount/100)));
        $amount_item = CSalesManager::round_up($amount_item);
        return $amount_item;
    }

    public function get_list_companies($customer_id=false,$feild=false) {
            global $AppUI;
            require_once (DP_BASE_DIR. "/modules/clients/company_manager.class.php");
            $cManager = new CompanyManager;
            if($customer_id)
                return $cManager->getCompanies($customer_id,-1, false,-1, -1, -1,$feild);
            return $cManager->getCompanies(false,-1, false,-1, -1, -1,$feild);
    }

    public function get_address_by_company($company_id) { // ham nay hien tai chua duoc su dung den
        $q = new DBQuery;
        $q->addTable('addresses');
        $q->addQuery('*');
        $q->addWhere("address_is_main = 1 AND company_id = ". intval($company_id));
        $rows = $q->loadList();
        if ($rows)
            return $rows[0];
        else
            return null;
    }

    public function get_address_by_id($address_id) { // ham nay hien tai chua duoc su dung den
        $q = new DBQuery;
        $q->addTable('addresses');
        $q->addQuery('*');
        $q->addWhere("address_id = ". intval($address_id));
        $rows = $q->loadList();
        if ($rows)
            return $rows[0];
        else
            return null;
    }

    /**
     * XXX
     *
     * @param  int $is_quotation = null XXX
     * @access public
     */
    public function create_invoice_or_quotation_no($is_quotation = false,$dept_id=false) {
        
        global $AppUI,$CNextNumber;
        //Lay departmat cua user hien tai
        require_once(DP_BASE_DIR . '/modules/departments/CDepartment.php');
        $CDepManager = new CDepartment();
        $department_user_id = "";
        if($dept_id)
        {
            $departmen_arr = $CDepManager->getDepartment($dept_id);
            $department_user_id = $departmen_arr[0]['dept_id'];
        }
        
        if (!$is_quotation) {
            $tbl = 'sales_invoice';
            $query = 'invoice_no';
            $order = 'invoice_no';
            $prefix = 'I-';
        } else {
            $tbl = 'sales_quotation';
            $query = 'quotation_no';
            $order = 'quotation_no';
            $prefix = 'Q-';
            $next_number = $CNextNumber->get_number('sales_quotaiton', $dept_id);
            $next_quotation = $next_number[0]['number_value'];
        }
            $q = new DBQuery();
            $q->addTable($tbl);
            $q->addQuery($query);
            if(!$is_quotation)
            {
                $q->addWhere('invoice_no != "Draft" && invoice_status!=5');
            }
            else
            {
                $q->addWhere('department_id='.intval($department_user_id));
                $q->addWhere('quotation_no NOT IN ("Q/ACMV/2014/006_R1","Q/ACMV/2014/005_R1","PAS000ECO15000405/1","NEA000ECO15000428/1")');
                $prefix = $departmen_arr[0]['department_no'].'-';
            }
            $q->addOrder($order.' ASC');
            $rows = $q->loadList();
            $int_map = array ('0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0');
            
            if (count($rows)>0) { // Neu ton tai > 1 ban ghi trong csdl
                    //$part_1 = date('Y', time()); // lay ra Nam hien tai
               
                    foreach ($rows as $rows_sales) {
                        $sales_no_before = $rows_sales[$query];
                    }
                    if($next_quotation)
                        $sales_no_before = $next_quotation;
//                    if($tmp==false){
//                        return $prefix . "000001";
//                    }

                    //if (intval($part_1) == intval($year)) { // neu nam hien tai va nam trong $sales_no duoc cat = nhau
                        $char_map = array(  'Z' => 'A', 'A' => 'B', 'B' => 'C', 'C' => 'D', 'D' => 'E', 'E' => 'F', 'F' => 'G', 'G' => 'H', 'H' => 'I',
                                            'I' => 'J', 'J' => 'K', 'K' => 'L', 'L' => 'M', 'M' => 'N', 'N' => 'O', 'O' => 'P', 'P' => 'Q', 'Q' => 'R',
                                            'R' => 'S', 'S' => 'T', 'T' => 'U', 'U' => 'V', 'V' => 'W', 'W' => 'X', 'X' => 'Y', 'Y' => 'Z',

                                            'z' => 'a', 'a' => 'b', 'b' => 'c', 'c' => 'd', 'd' => 'e', 'e' => 'f', 'f' => 'g', 'g' => 'h', 'h' => 'i',
                                            'i' => 'j', 'j' => 'k', 'k' => 'l', 'l' => 'm', 'm' => 'n', 'n' => 'o', 'o' => 'p', 'p' => 'q', 'q' => 'r',
                                            'r' => 's', 's' => 't', 't' => 'u', 'u' => 'v', 'v' => 'w', 'w' => 'x', 'x' => 'y', 'y' => 'z','/'=>'/',

                                            '0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0',
                                            '' => '0'
                            ); // tao mang cac ky tu can thay doi khi biet ky tu truoc do. viec khai bao nay se tiet kiem code

                        
                        $path2_str = $sales_no_before; // cat lay ra 6 ky tu cuoi trong chuoi $sales_no
                        $count_str = strlen($path2_str)-1;
                        if($path2_str=="")
                            $path2_str = 0;
                        $break = false; // khai bao 1 bien de thuc hien ngat loop khi phu hop
                        $maxchar = false;
                        $zero = false;
                        for ($i=$count_str; $i >= 0; $i-- ) { // lap chuoi $path2_str tu cuoi cung ve dau tien
                            if (!$break) { // neu van thoa man
                                if ($path2_str[$i] != 9 && $path2_str[$i] != 'z' && $path2_str[$i] != 'Z') // neu cac ky tu duoc lap qua khong phai ky tu cuoi cung trong day
                                    $break = true;
                                $path2_str[$i] = $char_map[$path2_str[$i]];
                            } else {
                                break;
                            }
                        }
                        //$path2_str
                        // xu ly truong hop gia tri max 999 va zzz
                        for($i=$count_str; $i >= 0; $i--)
                            if($sales_no_before[$i]!="z"){
                                $maxchar=true;
                                break;
                            }
                        if($maxchar==false)
                            $path2_str=$path2_str.a;
                        if($sales_no_before[0] == "9")
                            $path2_str="1".$path2_str;
//                        if($zero == true){
//                            $path2_str=$sales_no_before+1;
//                            
//                        }
                        // Neu dau "/" o cuoi them ky tu 0 vao sau.
                        if($sales_no_before[$count_str]=="/" || $sales_no_before[$count_str]=="-" || $sales_no_before[$count_str]==",")
                            $path2_str = $sales_no_before."0";
                        
                        $sales_no =$path2_str;
                    } else {
                            $part_2 = $prefix.'000001';
                            $sales_no = $part_2;
                    }

            return $sales_no;
            
//                $sql=mysql_query("select * from sales_quotation where quotation_no='$prefix . $sales_no'");
//     $dong=mysql_num_rows($sql);
//  if ($dong>0)
//   echo "Username đã có người sử dụng, bạn vui lòng chọn một Username khác!";

    }
    
    //11-02-2012 - tao moi quotaion_no
        public function create_quotation_no($is_quotation = false) {

            $tbl = 'sales_quotation';
            $query = 'quotation_no';
            $order = 'quotation_id';
            $prefix = 'Q-';
        
            $q = new DBQuery();
            $q->addTable($tbl);
            $q->addQuery($query);
            $q->setLimit(1, 0);
            $q->addOrder($order . ' DESC');
            $rows = $q->loadList();

            if ($rows) { // Neu ton tai > 1 ban ghi trong csdl
                    $part_1 = date('Y', time()); // lay ra Nam hien tai
                    $sales_no_before = ''. $rows[0][$query] .''; // lay ra $sales_no truoc

                    $year = substr($sales_no_before, 2, 4); // cat ra $sales_no de xem nam cua nhung $sales_no nay la nam nao

                    if (intval($part_1) == intval($year)) { // neu nam hien tai va nam trong $sales_no duoc cat = nhau
                        $char_map = array(  'Z' => 'A', 'A' => 'B', 'B' => 'C', 'C' => 'D', 'D' => 'E', 'E' => 'F', 'F' => 'G', 'G' => 'H', 'H' => 'I',
                                            'I' => 'J', 'J' => 'K', 'K' => 'L', 'L' => 'M', 'M' => 'N', 'N' => 'O', 'O' => 'P', 'P' => 'Q', 'Q' => 'R',
                                            'R' => 'S', 'S' => 'T', 'T' => 'U', 'U' => 'V', 'V' => 'W', 'W' => 'X', 'X' => 'Y', 'Y' => 'Z',

                                            'z' => 'a', 'a' => 'b', 'b' => 'c', 'c' => 'd', 'd' => 'e', 'e' => 'f', 'f' => 'g', 'g' => 'h', 'h' => 'i',
                                            'i' => 'j', 'j' => 'k', 'k' => 'l', 'l' => 'm', 'm' => 'n', 'n' => 'o', 'o' => 'p', 'p' => 'q', 'q' => 'r',
                                            'r' => 's', 's' => 't', 't' => 'u', 'u' => 'v', 'v' => 'w', 'w' => 'x', 'x' => 'y', 'y' => 'z',

                                            '0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0'
                            ); // tao mang cac ky tu can thay doi khi biet ky tu truoc do. viec khai bao nay se tiet kiem code

                        $path2_str = substr($sales_no_before, 6, 6); // cat lay ra 6 ky tu cuoi trong chuoi $sales_no
                        $break = false; // khai bao 1 bien de thuc hien ngat loop khi phu hop
                        for ($i = 5; $i >= 0; $i-- ) { // lap chuoi $path2_str tu cuoi cung ve dau tien
                            if (!$break) { // neu van thoa man
                                if ($path2_str[$i] != 9 && $path2_str[$i] != 'z' && $path2_str[$i] != 'Z') // neu cac ky tu duoc lap qua khong phai ky tu cuoi cung trong day
                                    $break = true;
                                $path2_str[$i] = $char_map[$path2_str[$i]];
                            } else {
                                break;
                            }
                        }
//                        $len = strlen($path2_str);
//                        if ($len <= 9)
                        $sales_no = $part_1 . $path2_str;
                    } else {
                            $part_2 = '000001';
                            $sales_no = $part_1 . $part_2;
                    }
            } else {
                    $part_1 = date('Y', time());
                    $part_2 = '000001';
                    $sales_no = $part_1 . $part_2;
            }

            return $prefix . $sales_no;

    }
    //tao moi quotaion_no - end
    
    public function create_receipt_no() {

            $tbl = 'sales_payment';
            $query = 'payment_receipt_no';
            $order = 'payment_receipt_no';
            $prefix = 'R-';
        
            $q = new DBQuery();
            $q->addTable($tbl);
            $q->addQuery($query);
            //$q->setLimit(1, 0);
            $q->addOrder($order . ' DESC');
            $rows = $q->loadList();
            
            if ($rows) { // Neu ton tai > 1 ban ghi trong csdl
                    $tmp=false;
                    foreach ($rows as $rows_sales) {
                        $sales_no1 = $rows_sales[$query];
                        if(strlen($sales_no1)==8){
                            $sales_no_before = $rows_sales[$query];
                            $tmp=true;
                            break;
                        }
                    }
                    if($tmp==false)
                        return $prefix . "000001";
                    //$sales_no_before = ''. $rows[0][$query] .''; // lay ra $sales_no truoc

                        $char_map = array(  'Z' => 'A', 'A' => 'B', 'B' => 'C', 'C' => 'D', 'D' => 'E', 'E' => 'F', 'F' => 'G', 'G' => 'H', 'H' => 'I',
                                            'I' => 'J', 'J' => 'K', 'K' => 'L', 'L' => 'M', 'M' => 'N', 'N' => 'O', 'O' => 'P', 'P' => 'Q', 'Q' => 'R',
                                            'R' => 'S', 'S' => 'T', 'T' => 'U', 'U' => 'V', 'V' => 'W', 'W' => 'X', 'X' => 'Y', 'Y' => 'Z',

                                            'z' => 'a', 'a' => 'b', 'b' => 'c', 'c' => 'd', 'd' => 'e', 'e' => 'f', 'f' => 'g', 'g' => 'h', 'h' => 'i',
                                            'i' => 'j', 'j' => 'k', 'k' => 'l', 'l' => 'm', 'm' => 'n', 'n' => 'o', 'o' => 'p', 'p' => 'q', 'q' => 'r',
                                            'r' => 's', 's' => 't', 't' => 'u', 'u' => 'v', 'v' => 'w', 'w' => 'x', 'x' => 'y', 'y' => 'z',

                                            '0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0'
                            ); // tao mang cac ky tu can thay doi khi biet ky tu truoc do. viec khai bao nay se tiet kiem code

                        $path2_str = substr($sales_no_before, 2, 6); // cat lay ra 6 ky tu cuoi trong chuoi $sales_no
                        $break = false; // khai bao 1 bien de thuc hien ngat loop khi phu hop
                        for ($i = 5; $i >= 0; $i-- ) { // lap chuoi $path2_str tu cuoi cung ve dau tien
                            if (!$break) { // neu van thoa man
                                if ($path2_str[$i] != 9 && $path2_str[$i] != 'z' && $path2_str[$i] != 'Z') // neu cac ky tu duoc lap qua khong phai ky tu cuoi cung trong day
                                    $break = true;
                                $path2_str[$i] = $char_map[$path2_str[$i]];
                            } else {
                                break;
                            }
                        }
//                        $len = strlen($path2_str);
//                        if ($len <= 9)
                        $sales_no = $path2_str;
            } else {
                    $part_2 = '000001';
                    $sales_no = $part_2;
            }

            return $prefix . $sales_no;

    }
    
    
    /**
     * XXX
     *
     * @param  string $obj_revision XXX
     * @param  string $obj_no = null XXX
     * @access public
     */
    public function create_invoice_or_quotation_revision($obj_revision, $obj_no = null, $quotation_no = null) {
            $suffix_count = 3;
            if($quotation_no)
                $suffix_count=2;
        if ($obj_no == null && $obj_revision != '') {
            $obj_revision_substr_orginal = substr($obj_revision, 0, -$suffix_count); // cat bo 3 ky tu cuoi cung trong chuoi
            $obj_revision_substr = substr($obj_revision, -$suffix_count); // cat lay 3 ky tu cuoi cung trong chuoi
            if ($obj_revision_substr == '999') {
                return null;
            } else {
                    $char_map = array( '0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0'); // tao mang cac ky tu can thay doi khi biet ky tu truoc do. viec khai bao nay se tiet kiem code
            
                    $break = false; // khai bao 1 bien de thuc hien ngat loop khi phu hop
                    for ($i = ($suffix_count - 1); $i >= 0; $i-- ) { // lap chuoi $suffix_count tu cuoi cung ve dau tien
                        if (!$break) { // neu van thoa man
                            if ($obj_revision_substr[$i] != 9) // neu cac ky tu duoc lap qua khong phai ky tu cuoi cung trong day
                                    $break = true;
                            $obj_revision_substr[$i] = $char_map[$obj_revision_substr[$i]];
                        } else {
                            break;
                        }
                    }
            //$a = str_replace($obj_revision_substr_old, $obj_revision_substr, $obj_revision);
            //print_r($a);
            //print_rv($a);
                    if(!$quotation_no) {
                        return $obj_revision_substr_orginal . $obj_revision_substr;
                    } else {
                        return $quotation_no . '-' . $obj_revision_substr;
                    }
            }
        } else {
            if(!$quotation_no) {
                return $obj_no . '-001';
            } else {
                return $quotation_no . '-00';
            }
        }
        
    }
    
    public function create_quotation_revision($obj_revision, $obj_no = null, $quotation_no = null)
    {
       if ($obj_no == null && $obj_revision != '') {
            $is_R = strripos($obj_revision,'R');      

            if(!$is_R)
            {
                $obj_revision_substr = strrchr($obj_revision, '-');
                $suffix_count = strlen($obj_revision_substr)-1;
                $obj_revision_substr=  substr($obj_revision_substr, -$suffix_count);
                $char_map = array( '0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0'); // tao mang cac ky tu can thay doi khi biet ky tu truoc do. viec khai bao nay se tiet kiem code

                $break = false; // khai bao 1 bien de thuc hien ngat loop khi phu hop
                for ($i = ($suffix_count - 1); $i >= 0; $i-- ) { // lap chuoi $suffix_count tu cuoi cung ve dau tien
                    if (!$break) { // neu van thoa man
                        if ($obj_revision_substr[$i] != 9) // neu cac ky tu duoc lap qua khong phai ky tu cuoi cung trong day
                                $break = true;
                        $obj_revision_substr[$i] = $char_map[$obj_revision_substr[$i]];
                    } else {
                        break;
                    }
                }
            }
            else
            {
                $obj_revision_substr = strrchr($obj_revision, 'R');
                $suffix_count = strlen($obj_revision_substr)-1;

                $obj_revision_substr = substr($obj_revision, -$suffix_count);
                $obj_revision_substr = intval($obj_revision_substr)+1;
                $obj_revision_substr = 'R'.$obj_revision_substr;
            }
            
            return  $quotation_no.'-'.$obj_revision_substr;
        } else {
            return $quotation_no . '-R0';
        }
    }




    public function create_invoice_revision($obj_revision, $obj_no = null, $invoice_no = null) {
        
        $suffix_count = 3;
        
        if ($obj_no == null && $obj_revision != '') {
            $obj_revision_substr_orginal = substr($obj_revision, 0, -$suffix_count); // cat bo 3 ky tu cuoi cung trong chuoi
            $obj_revision_substr = substr($obj_revision, -$suffix_count); // cat lay 3 ky tu cuoi cung trong chuoi
            if ($obj_revision_substr == '999') {
                return null;
            } else {
                    $char_map = array( '0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0'); // tao mang cac ky tu can thay doi khi biet ky tu truoc do. viec khai bao nay se tiet kiem code
            
                    $break = false; // khai bao 1 bien de thuc hien ngat loop khi phu hop
                    for ($i = ($suffix_count - 1); $i >= 0; $i-- ) { // lap chuoi $suffix_count tu cuoi cung ve dau tien
                        if (!$break) { // neu van thoa man
                            if ($obj_revision_substr[$i] != 9) // neu cac ky tu duoc lap qua khong phai ky tu cuoi cung trong day
                                    $break = true;
                            $obj_revision_substr[$i] = $char_map[$obj_revision_substr[$i]];
                        } else {
                            break;
                        }
                    }
            //$a = str_replace($obj_revision_substr_old, $obj_revision_substr, $obj_revision);
            //print_r($a);
            //print_rv($a);
                    if(!$invoice_no) {
                        return $obj_revision_substr_orginal . $obj_revision_substr;
                    } else {
                        return $invoice_no . '-' . $obj_revision_substr;
                    }
            }
        } else {
            if(!$invoice_no) {
                return $obj_no . '-001';
            } else {
                return $invoice_no . '-001';
            }
        }
        
    }
    

//Update Quotation_revision
    public function update_invoice_or_quotation_revision($obj_revision, $obj_no = null, $quotation_no = null) {
        
        $suffix_count = 3;
        if ($obj_no == null && $obj_revision != '') {
            $obj_revision_substr_orginal = substr($obj_revision, 0, -$suffix_count); // cat bo 3 ky tu cuoi cung trong chuoi
            $obj_revision_substr = substr($obj_revision, -$suffix_count); // cat lay 3 ky tu cuoi cung trong chuoi
            if ($obj_revision_substr == '999') {
                return null;
            } else {
                    $char_map = array( '0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0'); // tao mang cac ky tu can thay doi khi biet ky tu truoc do. viec khai bao nay se tiet kiem code
            
                    $break = false; // khai bao 1 bien de thuc hien ngat loop khi phu hop
                    for ($i = ($suffix_count - 1); $i >= 0; $i-- ) { // lap chuoi $suffix_count tu cuoi cung ve dau tien
                        if (!$break) { // neu van thoa man
                            if ($obj_revision_substr[$i] != 9) // neu cac ky tu duoc lap qua khong phai ky tu cuoi cung trong day
                                    $break = true;
                            $obj_revision_substr[$i] = $char_map[$obj_revision_substr[$i]];
                        } else {
                            break;
                        }
                    }
            //$a = str_replace($obj_revision_substr_old, $obj_revision_substr, $obj_revision);
            //print_r($a);
            //print_rv($a);
                    if(!$quotation_no) {
                        return $obj_revision_substr_orginal . $obj_revision_substr;
                    } else {
                        return $quotation_no . '-' . $obj_revision_substr;
                    }
            }
        } else {
            if(!$quotation_no) {
                return $obj_no . '-001';
            } else {
                return $quotation_no . '-001';
            }
        }
        
    }
    
//    public function create_invoice_or_quotation_revision($obj_revision, $obj_no = null, $quotation_no = null, $quotation_id) {
//        
//        $suffix_count = 3;
//        
//        if ($obj_no == null && $obj_revision != '') {
//            $obj_revision_substr_orginal = substr($obj_revision, 0, -$suffix_count); // cat bo 3 ky tu cuoi cung trong chuoi
//            $obj_revision_substr = substr($obj_revision, -$suffix_count); // cat lay 3 ky tu cuoi cung trong chuoi
//            if ($obj_revision_substr == '999') {
//                return null;
//            } else {
//                    $char_map = array( '0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0'); // tao mang cac ky tu can thay doi khi biet ky tu truoc do. viec khai bao nay se tiet kiem code
//            
//                    $break = false; // khai bao 1 bien de thuc hien ngat loop khi phu hop
//                    for ($i = ($suffix_count - 1); $i >= 0; $i-- ) { // lap chuoi $suffix_count tu cuoi cung ve dau tien
//                        if (!$break) { // neu van thoa man
//                            if ($obj_revision_substr[$i] != 9) // neu cac ky tu duoc lap qua khong phai ky tu cuoi cung trong day
//                                    $break = true;
//                            $obj_revision_substr[$i] = $char_map[$obj_revision_substr[$i]];
//                        } else {
//                            break;
//                        }
//                    }
//            //$a = str_replace($obj_revision_substr_old, $obj_revision_substr, $obj_revision);
//            //print_r($a);
//            //print_rv($a);
//                    if(!$quotation_no) {
//                        return $obj_revision_substr_orginal . $obj_revision_substr;
//                    } else {
//                        $obj = new CQuotationManager();
//                        $qoutation_rev_arr = $obj->get_db_quotation_rev($quotation_id);
//                        foreach ($qoutation_rev_arr as $rev) {
//                            $quotation_revision = $rev['quotation_revision'];
//                            $revisions = substr($quotation_revision, 0, 12);
//                            $revisions = $quotation_no;
//                            return $revisions. '-' . $obj_revision_substr;
//                        }
//                        //return $revisions = $quotation_no. '-' . $obj_revision_substr;
//                        //print_r($revisions);
//                    }
//            }
//        } else {
//            if(!$quotation_no) {
//                return $obj_no . '-001';
//            } else {
//                return $quotation_no . '-001';
//            }
//        }
//        
//    }
    
    
    /**
     * XXX
     * 
     * @param  int $customer XXX
     * @return boolean XXX
     * @access public
     */
    public function get_customer_field_by_id($customer_id, $customer_field) {
        
            $q = new DBQuery();
            $q->addTable('clients');
            $q->addQuery($customer_field);
            $q->addWhere('company_id = '. intval($customer_id));
            $rows = $q->loadList();
            return $rows[0][$customer_field];
    }
    
    public function get_list_address($customer_id=false,$address_id=false,$add=false) {
            $q = new DBQuery();
            $q->addTable('addresses');
            $q->addQuery('address_id, address_street_address_1, address_street_address_2, address_type, address_postal_zip_code,address_branch_name');
            if($customer_id)
                $q->addWhere('company_id = '. intval($customer_id));
            if($address_id)
                $q->addWhere ('address_id='.  intval($address_id));
//            if(isset($add) & $add>0)
//                $q->addWhere ('address_type <>'.  intval($add));
            $q->addOrder('address_type DESC');
            return $q->loadList();
    }
    
    public function get_list_quotation_status($quotation_id) {
            $q = new DBQuery();
            $q->addTable('sales_quotation');
            $q->addQuery('quotation_status');
            $q->addWhere('quotation_id = '. intval($quotation_id));
            return $q->loadList();
    }
    
    public function get_list_attention($customer_id) {
            $q = new DBQuery();
            $q->addTable('contacts', 'tbl1');
            $q->addQuery('tbl2.company_id, tbl2.contact_id, tbl1.contact_first_name, tbl1.contact_last_name, tbl1.contact_title');
            $q->addJoin('company_contact', 'tbl2', "tbl2.contact_id = tbl1.contact_id");
            $q->addWhere('tbl2.company_id = '. intval($customer_id));
            return $q->loadList();
    }
    
    public static function get_supplier_info() {
            global $AppUI;
            require_once (DP_BASE_DIR. "/modules/system/owner_system_info.class.php");
            return owner_system_info::get_owner_system_info();
    }
    
    public function get_list_quotation_rev($quotation_no) {
        $q = new DBQuery();
            $q->addTable('sales_quotation', 'tbl1');
            $q->addQuery('tbl1.quotation_id, tbl2.quotation_revision_id');
            $q->addJoin('sales_quotation_revision', 'tbl2', "tbl2.quotation_id = tbl1.quotation_id");
            $q->addWhere('tbl2.quotation_no = '. intval($quotation_no));
            return $q->loadList();
    }
    function get_list_Invoice_Customer() {
        
    }
    
    
    // function nay duoc goi tu module jobs tab service order
    public static function get_quotation_byCustomer($client_id=false, $address_id=false, $qoutation_id=false) {
        $q = new DBQuery();
        $q->addTable('sales_quotation', 'tbl1');
        //$q->addJoin('sales_quotation_revision', 'tbl2', 'tbl1.quotation_id = tbl2.quotation_id');
        $q->leftJoin('sales_quotation_revision', 'tbl2', 'tbl1.quotation_id = tbl2.quotation_id');
        $q->addQuery('tbl1.quotation_id, tbl1.quotation_no, tbl2.quotation_revision_id,tbl1.quotation_status');
        if($client_id && $address_id) {
            $q->addWhere('tbl1.customer_id = '. $client_id.' AND tbl1.job_location_id = '.$address_id.' AND tbl1.quotation_no !="0"');
        }
        if($client_id && $address_id && $qoutation_id) {
            $q->addWhere('tbl1.customer_id = '. $client_id.' AND tbl1.job_location_id = '.$address_id.' AND tbl1.quotation_id='.$qoutation_id.' AND tbl1.quotation_no !="0"');
        }
        if($qoutation_id){
            $q->addWhere('tbl1.quotation_id = '.$qoutation_id.' AND tbl1.quotation_no !="0"');
        }
        $q->addGroup('tbl1.quotation_id');
        $results = $q->loadList();
        return $results;
    }
    public static function get_Invoice_byCustomer($client_id=false, $address_id=false, $invoice_id=false) {
        $q = new DBQuery();
        $q->addTable('sales_invoice', 'tbl1');
        $q->addJoin('sales_invoice_revision', 'tbl2', 'tbl1.invoice_id = tbl2.invoice_id');
        $q->addQuery('tbl1.invoice_id, tbl1.invoice_no, tbl2.invoice_revision_id,tbl1.invoice_status');
        if($client_id && $address_id) {
            $q->addWhere('tbl1.customer_id = '. $client_id.' AND tbl1.job_location_id = '.$address_id.' AND tbl1.invoice_no !="0"');
        }
        if($client_id && $address_id && $invoice_id) {
            $q->addWhere('tbl1.customer_id = '. $client_id.' AND tbl1.job_location_id = '.$address_id.' AND tbl1.invoice_id='.$invoice_id.' AND tbl1.invoice_no !="0"');
        }
        if($invoice_id) {
            $q->addWhere('tbl1.invoice_id = '. $invoice_id.' AND tbl1.invoice_no !="0"');
        }
        $q->addGroup('tbl1.invoice_id');
        $results = $q->loadList();
        return $results;
    }
    
    function get_Customer_By_ContractorCustomer($customer_id){
            $q = new DBQuery();
            $q->addTable('addresses','tbl1');
            $q->addQuery('tbl1.company_id,tbl2.company_name,tbl2.company_id');
            $q->addJoin('clients', 'tbl2', "tbl2.company_id=tbl1.company_id");
            $q->addWhere('tbl1.address_contractor = '. intval($customer_id));
            $q->addGroup('tbl2.company_id');
            return $q->loadList();
    }
    
    function get_Customer_By_AgentCustomer($customer_id){
        $q = new DBQuery();
        $q->addTable('address_agent_company','tbl1');
        $q->addQuery('tbl2.company_id,tbl3.company_name,tbl1.address_id');
        $q->addJoin('addresses', 'tbl2', 'tbl2.address_id = tbl1.address_id');
        $q->addJoin('clients', 'tbl3', 'tbl3.company_id=tbl2.company_id');
        $q->addWhere('tbl1.company_id='.intval($customer_id));
        $q->addGroup('tbl2.company_id');
        return $q->loadList();
    }
    
    function get_AgentCustomer_by_customer($customer_id){
        $q = new DBQuery();
        $q->addTable('addresses','tbl1');
        $q->addQuery('tbl2.address_id,tbl2.company_id,tbl3.company_name');
        $q->addJoin('address_agent_company', 'tbl2', 'tbl2.address_id = tbl1.address_id');
        $q->addJoin('clients', 'tbl3', 'tbl3.company_id=tbl2.company_id');
        $q->addWhere('tbl1.company_id = '.intval($customer_id));
        $q->addGroup('tbl2.company_id');
        return $q->loadList();
    }
    
    function get_ContractorCustomer_by_customer($customer_id){
        $q = new DBQuery();
        $q->addTable('addresses','tbl1');
        $q->addQuery('tbl1.address_contractor,tbl2.company_name,tbl2.company_id');
        $q->addJoin('clients', 'tbl2', 'tbl1.address_contractor=tbl2.company_id');
        $q->addWhere('tbl1.company_id = '.  intval($customer_id));
        $q->addGroup('tbl2.company_id');
        return $q->loadList();
    }
// Ham chuyen the HTML ve nguyen dang cua no de hien thi ra man hinh (the <br/> khong bi chuyen doi >
    function htmlChars($str){
        $str=  preg_replace("/<(?!br)/", '&lt;', $str);
        $str=  preg_replace("/'/", '&#039;', $str);
        $str=  preg_replace('/"/', '&quot;', $str);
        $str=  preg_replace('/\n/', '<br/>', $str);
        $str=  preg_replace('/  /', '&nbsp;', $str);
        return $str;
    }
    // Hàm lam tron so thap phan thu 2 tang len 1 don vi
    // VD:  123.1234->123.13
    //      123.1262->123.13  
    public static function round_up($x){
        $r = round($x,2);
        $tmp = $x-$r;
        $tmp = round($tmp,4);
        if($tmp>0)
            return $r+0.01;
        return $r;
    }
    
    
    public function create_creditNote_or_receipt_no($is_credit=false) {

        if ($is_credit) {
            $tbl = 'sales_credit_note';
            $query = 'credit_note_no';
            $order = 'credit_note_id';
            $prefix = 'V-';
        } else {
            $tbl = 'sales_payment';
            $query = 'payment_receipt_no';
            $order = 'payment_id';
            $prefix = 'R-';
        }
            $q = new DBQuery();
            $q->addTable($tbl);
            $q->addQuery($query);
            $q->addOrder($order . ' ASC');
            $rows = $q->loadList();

            $int_map = array ('0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0');
            
            if ($rows) { // Neu ton tai > 1 ban ghi trong csdl
                    //$part_1 = date('Y', time()); // lay ra Nam hien tai
               
                    foreach ($rows as $rows_sales) {
                        $sales_no_before = $rows_sales[$query];
                    }
//                    if($tmp==false){
//                        return $prefix . "000001";
//                    }

                    //if (intval($part_1) == intval($year)) { // neu nam hien tai va nam trong $sales_no duoc cat = nhau
                        $char_map = array(  'Z' => 'A', 'A' => 'B', 'B' => 'C', 'C' => 'D', 'D' => 'E', 'E' => 'F', 'F' => 'G', 'G' => 'H', 'H' => 'I',
                                            'I' => 'J', 'J' => 'K', 'K' => 'L', 'L' => 'M', 'M' => 'N', 'N' => 'O', 'O' => 'P', 'P' => 'Q', 'Q' => 'R',
                                            'R' => 'S', 'S' => 'T', 'T' => 'U', 'U' => 'V', 'V' => 'W', 'W' => 'X', 'X' => 'Y', 'Y' => 'Z',

                                            'z' => 'a', 'a' => 'b', 'b' => 'c', 'c' => 'd', 'd' => 'e', 'e' => 'f', 'f' => 'g', 'g' => 'h', 'h' => 'i',
                                            'i' => 'j', 'j' => 'k', 'k' => 'l', 'l' => 'm', 'm' => 'n', 'n' => 'o', 'o' => 'p', 'p' => 'q', 'q' => 'r',
                                            'r' => 's', 's' => 't', 't' => 'u', 'u' => 'v', 'v' => 'w', 'w' => 'x', 'x' => 'y', 'y' => 'z',

                                            '0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6', '6' => '7', '7' => '8', '8' => '9', '9' => '0',
                                            '' => '0','-'=>'-'
                            ); // tao mang cac ky tu can thay doi khi biet ky tu truoc do. viec khai bao nay se tiet kiem code

                        
                        $path2_str = $sales_no_before; // cat lay ra 6 ky tu cuoi trong chuoi $sales_no
                        $count_str = strlen($path2_str)-1;
                        if($path2_str=="")
                            $path2_str = 0;
                        $zero=false;
                        $maxchar=false;
                        $break = false; // khai bao 1 bien de thuc hien ngat loop khi phu hop
                        for ($i=$count_str; $i >= 0; $i-- ) { // lap chuoi $path2_str tu cuoi cung ve dau tien
                            if (!$break) { // neu van thoa man
                                if ($path2_str[$i] != 9 && $path2_str[$i] != 'z' && $path2_str[$i] != 'Z' || $path2_str[$i]== '-') // neu cac ky tu duoc lap qua khong phai ky tu cuoi cung trong day
                                    $break = true;
                                $path2_str[$i] = $char_map[$path2_str[$i]];
                            } else {
                                break;
                            }
                        }
                        // xu ly truong hop gia tri max 999 va zzz
                        for($i=$count_str; $i >= 0; $i--)
                            if($sales_no_before[$i]!="z"||$sales_no_before[$i]!="Z"){
                                $maxchar=true;
                                break;
                            }
                        for($i=$count_str; $i >= 0; $i--)
                            if($sales_no_before[$i]!=9){
                                $zero=true;
                                break;
                            }
                        if($maxchar==false)
                            $path2_str=$path2_str.a;
                        if($zero == false)
                            $path2_str=$path2_str+1;

                        $sales_no =$path2_str;
                    } else {
                            $part_2 = $prefix.'000001';
                            $sales_no = $part_2;
                    }

            return $sales_no;
            
//                $sql=mysql_query("select * from sales_quotation where quotation_no='$prefix . $sales_no'");
//     $dong=mysql_num_rows($sql);
//  if ($dong>0)
//   echo "Username đã có người sử dụng, bạn vui lòng chọn một Username khác!";

    }
    function total_creditNote_amount($credit_note_id){
        $q = new DBQuery();
        $q ->addTable('sales_credit_note_item');
        $q->addQuery('Sum(credit_note_item_amount) AS totalAmount');
        $q->addWhere('credit_note_id='.intval($credit_note_id));
        return $q->loadList();
    }
    function total_creditNote_amount_and_tax($credit_note_id,$date_from=false,$date_to=false){
        $q = new DBQuery();
        $q ->addTable('sales_credit_note','tbl1');
        $q->addQuery('tbl2.tax_rate,tbl1.tax_edit_value');
        $q->addJoin('sales_tax','tbl2', 'tbl2.tax_id=tbl1.credit_note_tax_id');
        $q->addWhere('tbl1.credit_note_id='.intval($credit_note_id));
        if($date_to)
            $q->addWhere ('tbl1.credit_note_date <= "'.$date_to.'"');
        if($date_from)
            $q->addWhere ('tbl1.credit_note_date >= "'.$date_from.'"');
        $row = $q->loadList();
        $total_amount =0;
        if(count($row)>0)
        {
            $tax_value = $row[0]['tax_rate'];     

            $row_item = $this->total_creditNote_amount($credit_note_id);
            $credit_amount = $row_item[0]['totalAmount'];
    //        
            $caculating_tax = floatval($credit_amount) * floatval($tax_value) / 100;
            $caculating_tax = $this->round_up($caculating_tax);
            if($row[0]['tax_edit_value']!=0)
                $caculating_tax = $row[0]['tax_edit_value'];
            $total_amount = $credit_amount + $caculating_tax;
        }
        return $total_amount;
    }
    function get_total_amount_paid($invoice_revistion_id){
            $q = new DBQuery();
            $q->addTable('sales_payment_detail');
            $q->addQuery('payment_amount');
            $q->addWhere('invoice_revision_id = '. intval($invoice_revistion_id));
            $rows = $q->loadList();
            foreach ($rows as $row) {
                $total_paid += floatval($row['payment_amount']);
            }
            $total_paid = $total_paid;
            return $total_paid;
    }
    function getQuotationsOfCompany($company_id = false, $address_id = false) {
        $q = new DBQuery();
        $q->addTable('sales_quotation');
        $q->addQuery('sales_quotation.quotation_id');
        if ($company_id)
            $q->addWhere('customer_id = '.intval($company_id));
        if ($address_id)
            $q->addWhere('address_id = '.intval($address_id));
        
        return $q->loadList();
    }
    function get_serviceoder_id($inv_or_quo_id,$type){
        $q = new DBQuery();
        $q->addTable('services_order_assign');
        $q->addQuery('*');
        $q->addWhere('type="'.$type.'"');
        $q->addWhere('inv_quotation_id='.intval($inv_or_quo_id));
        return $q->loadList();
    }
    function add_salesAttention($SalesAttentionObj){
        $CSalesAttention = new CSalesAttention();
        $CSalesAttention->bind($SalesAttentionObj);
        $CSalesAttention->store();
        return $CSalesAttention->sales_attention_id;
    }
    function get_salesAttention_by_SalesType($SalesType_id,$type_name=false){//$type_name{invoice, quotation, creditNote}
        $q = new DBQuery();
        $q->addTable('sales_attention');
        $q->addQuery('*');
        $q->addWhere('sales_type_id = '.intval($SalesType_id));
        if($type_name)
            $q->addWhere ('sales_type_name = "'.$type_name.'"');
        return $q->loadList();
    }
    function remove_salesAttention($sales_attention_id=false,$SalesType_id=false){
        if($sales_attention_id)
            $sql="delete from sales_attention where sales_attention_id = ".intval($sales_attention_id);
        if($SalesType_id)
            $sql="delete from sales_attention where sales_type_id = ".intval($SalesType_id);
        if(db_exec($sql))
            return true;
        return false;
    }
    function get_customer_name($customer_id){
        $q = new DBQuery();
        $q->addTable('clients');
        $q->addQuery('company_name,company_title');
        $q->addWhere('company_id='.intval($customer_id));
        return $q->loadList();
    }
    // Get db template pdf
    /*
     * param int $template_pdf_id XXX
     * return int template_default;
     */
    function get_template_pdf($template_pdf_id)
    {
        $q = new DBQuery();
        $q->addQuery('*');
        $q->addTable('sales_template_pdf');
        $q->addWhere('template_pdf_id='.intval($template_pdf_id));
        $row = $q->loadList();
        return $row[0]['template_default'];
    }
    function get_template_server($template_pdf_id)
    {
        $q = new DBQuery();
        $q->addQuery('*');
        $q->addTable('sales_template_pdf');
        $q->addWhere('template_pdf_id='.intval($template_pdf_id));
        $row = $q->loadList();
        return $row[0]['template_server'];
    }
    public static function negativeConverParenthesis($value)
    {
        $negative = "";
        $negative  = substr($value,0,1);
        if($negative=="-")
        {
            $value = substr($value,1);
            $value = "($".number_format($value,2).")";
        }
        else
            $value = "$".number_format($value,2);
        
        return $value;
    }
    
    public function HeaderPDFMycool($type=false)
    {
        require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
        require_once(DP_BASE_DIR."/modules/sales/CQuotationManager.php");
        require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
        $CTemplatePDF = new CTemplatePDF();
        
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        
        $files = list_files($url);
        $w="85%";
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
            $img = '<td width="16%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" /></td>';
        }
        
//        $files = scandir($url);
//        $i = count($files)-1;
//        $w="85%";
//        if (count($files) > 2 && $files[$i]!=".svn") {
//            $img = '<td width="16%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="95" /></td>';
//        }
        $supplier_arr = $this->get_supplier_info();
        if (count($supplier_arr) > 0) {
            $sales_owner_name = $supplier_arr['sales_owner_name'];
            $sales_owner_address =$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
            $phone =$supplier_arr['sales_owner_phone1'];
            $fax = $supplier_arr['sales_owner_fax'];
            $email =$supplier_arr['sales_owner_email'];
            $web = $supplier_arr['sales_owner_website'];
            $sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
            $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
        }
        
        if($fax!="")
        {
            $fax_mycool = ' / '.$fax;
        }
        $supplier = '<tr><td style="font-size:1.2em;"><b>'. $sales_owner_name .'</b></td></tr>';
        $supplier.= '<tr><td>'.$sales_owner_address.'</td></tr>';
        $supplier .= '<tr><td>Co. Reg. No: '. $sales_owner_gst_reg_no .'</td></tr>';
        $supplier .= '<tr><td>Tel: '.$phone.$fax_mycool.'</td></tr>';
        $supplier .= '<tr><td>Email Address: '.$email.'</td></tr>';
        
        $txt_header='<table border="0" cellspacing="0" cellpadding="0">
                <tr valign = "top" height="60">
                    '.$img.'
                    <td width="'.$w.'"><table border="0"  width="100%">'.$supplier.'</table></td>
                </tr>
           </table>';
        
        return $txt_header;
    }
    
    public function HeaderPDFVeltech($type=false)
    {
        require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");
        require_once(DP_BASE_DIR."/modules/sales/CQuotationManager.php");
        require_once(DP_BASE_DIR."/modules/sales/CInvoiceManager.php");
        $CTemplatePDF = new CTemplatePDF();
        
        $url = DP_BASE_DIR. '/modules/sales/images/logo/';
        $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
        
        $files = list_files($url);
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
            $img = '<td width="16%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="80" /></td>';
        }
        
//        $files = scandir($url);
//        $i = count($files)-1;
//        $w="85%";
//        if (count($files) > 2 && $files[$i]!=".svn") {
//            $img = '<td width="16%" rowspan="2" align="left"><img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="80" /></td>';
//        }
        $supplier_arr = $this->get_supplier_info();
        if (count($supplier_arr) > 0) {
            $sales_owner_name = $supplier_arr['sales_owner_name'];
            $sales_owner_address =$supplier_arr['sales_owner_address1'] .', '.$supplier_arr['sales_owner_address2'].', '.$supplier_arr['sales_owner_country'].' '.$supplier_arr['sales_owner_postal_code'];
            $phone =$supplier_arr['sales_owner_phone1'];
            $fax = $supplier_arr['sales_owner_fax'];
            $email =$supplier_arr['sales_owner_email'];
            $web = $supplier_arr['sales_owner_website'];
            $sales_owner_reg_no =  $supplier_arr['sales_owner_reg_no'] ;
            $sales_owner_gst_reg_no = $supplier_arr['sales_owner_gst_reg_no'] ;
        }
        
        if($fax!="")
        {
            $fax_mycool = ' / '.$fax;
        }
        $supplier = '<tr><td style="font-size:1.2em;"><b>'. $sales_owner_name .'</b></td></tr>';
        $supplier.= '<tr><td>'.$sales_owner_address.'</td></tr>';
        $supplier .= '<tr><td>Co. Reg. No: '. $sales_owner_gst_reg_no .'</td></tr>';
        $supplier .= '<tr><td>Tel: '.$phone.$fax_mycool.'</td></tr>';
        $supplier .= '<tr><td>Email Address: '.$email.'</td></tr>';
        
        $txt_header='<table border="0" cellspacing="0" cellpadding="0">
                <tr valign = "top" height="60">
                    '.$img.'
                    <td width="'.$w.'"><table border="0"  width="100%">'.$supplier.'</table></td>
                </tr>
           </table>';
        
        return $txt_header;
    }
    
    function info_amount_all_credit_note($credit_note_id,$date_from=false,$date_to=false){
        $q = new DBQuery();
        $q ->addTable('sales_credit_note','tbl1');
        $q->addQuery('tbl1.credit_note_id,tbl2.credit_note_item_amount');
        $q->addJoin('sales_credit_note_item','tbl2', 'tbl2.credit_note_id=tbl1.credit_note_id');
        
        if($date_to)
            $q->addWhere ('tbl1.credit_note_date <= "'.$date_to.'"');
        if($date_from)
            $q->addWhere ('tbl1.credit_note_date >= "'.$date_from.'"');
        $row = $q->loadList();
        $amount=0;$total=0;
        if(count($row)>0)
        {
            foreach ($row as $data)
            {
                $amount+=$data['credit_note_item_amount'];
                $total += $this->total_creditNote_amount_and_tax($data['credit_note_id']);
            }
        }
        $rows[0]['amount']=$amount;
        $rows[0]['total']=$total;
        return $rows[0];

    }
}


?>
