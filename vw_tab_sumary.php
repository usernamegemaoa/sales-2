<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once (DP_BASE_DIR."/modules/clients/company_manager.class.php");

define(FORMAT_DATE_DEFAULT, 'd/m/Y');

require_once 'CSalesManager.php';
require_once (DP_BASE_DIR."/modules/sales/CQuotationManager.php");

$CSalesManager = new CSalesManager();
$CQuotationManager = new CQuotationManager();


function __default() {
    
    $template = $_POST['template'];
    $quotation_id = $_POST['quotation_id'];
    $quotation_revision_id = $_POST['quotation_rev_id'];
    
    echo '<div id="div_info">';
            _form_info($quotation_id, $quotation_revision_id, $template);
    echo '</div>';
    echo '<div id="div_3">';
        show_html_table($quotation_id, $quotation_revision_id);
    echo '</div>';
    echo '<div id="div_total_note">';
         _form_total_and_note($quotation_id, $quotation_revision_id);
    echo '</div>';
}


function _form_info($quotation_id = 0, $quotation_revision_id = 0, $template) {

     global $AppUI, $CQuotationManager, $CSalesManager;
    include DP_BASE_DIR."/modules/sales/css/quotation_main.css.php";
    $url = DP_BASE_DIR. '/modules/sales/images/logo/';
    $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
    
        $files = list_files($url);
        if(count($files)>0)
        {
            $i = count($files)-1;
                $path_file = $url . $files[$i];
            $img = '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="100" width="150" /><br/>';
        }
    
//    $files = scandir($url);
//    if (count($files) > 2) {
//        $path_file = $url . $files[2];
//        $img = '<img src="'. $base_url . $files[2] .'" alt="Smiley face" height="100" width="150" /><br/>';
//    }

    if ($quotation_id != 0 && $quotation_revision_id != 0) {
        $quotation_details_arr = $CQuotationManager->get_db_quotation($quotation_id);
        $quotation_details_arr1 = $CQuotationManager->get_db_quotation_rev($quotation_id);
        if (count($quotation_details_arr) > 0) {
           $date_hidden = $quotation_details_arr[0]['quotation_date'];
            $date_display = date(FORMAT_DATE_DEFAULT, strtotime($date_hidden));
            $quotation_no = $quotation_details_arr[0]['quotation_no'];
            $quotation_rev = $quotation_details_arr1[0]['quotation_revision'];

            $sale_person_name = $quotation_details_arr[0]['quotation_sale_person'];
            $sale_person_email = $quotation_details_arr[0]['quotation_sale_person_email'];
            $sale_person_phone = $quotation_details_arr[0]['quotation_sale_person_phone'];

            $client_id = $quotation_details_arr[0]['customer_id'];
            $address_id = $quotation_details_arr[0]['address_id'];
            $attention_id = $quotation_details_arr[0]['attention_id'];

        } else {
            $date_hidden = date('Y-m-d', time());
            $date_display = date(FORMAT_DATE_DEFAULT, time());
            $quotation_no = '';
            $quotation_rev = '';

            $sale_person_name = '';
            $sale_person_email = '';
            $sale_person_phone = '';
        }
    } else {

            $date_hidden = date('Y-m-d', time());
            $date_display = date(FORMAT_DATE_DEFAULT, time());
            $quotation_no = $CSalesManager->create_invoice_or_quotation_no(true);
            $quotation_rev = $CSalesManager->create_invoice_or_quotation_revision('', $quotation_no);

            $sale_person_name = '';
            $sale_person_email = '';
            $sale_person_phone = '';
    }

    //Update Status (Anhnn)
    $quotation_status_arr = $CSalesManager->get_list_quotation_status($quotation_id);
    if (count($quotation_status_arr) > 0) {
        $status_option = '';
        foreach ($quotation_status_arr as $quotation_status) {
            $selected = '';
            if ($quotation_id == $quotation_status['quotation_id'])
                $selected = 'selected="selected"';
            $status_option .= '<option value="'. $quotation_status['quotation_id'] .'" '. $selected .'>'. $quotation_status['quotation_status'] .'</option>';
        }
    }

    $quotation_stt = dPgetSysVal('QuotationStatus');
    $quotation_stt_dropdown = arraySelect($quotation_stt, 'quotation_status_id', 'id="quotation_status_id"  class="text" size="1" onchange="update_status('. $quotation_id .', this.value, '.$quotation_status['quotation_status'].' , \' quotation_status \')"', $quotation_status['quotation_status'] , true);

    $supplier_arr = $CSalesManager->get_supplier_info();
    $supplier = '<p>'. $supplier_arr['sales_owner_name'] .'</p>';
    $supplier .= '<p>'. $supplier_arr['sales_owner_address1'] .'</p>';
    $supplier .= '<p>'. $supplier_arr['sales_owner_address2'] .'</p>';
    $supplier .= '<p>'. $supplier_arr['sales_owner_phone1'] .', '. $supplier_arr['sales_owner_fax'] .', '. $supplier_arr['sales_owner_email'] .', '. $supplier_arr['sales_owner_website'] .'</p>';
    $supplier .= '<p>'. $AppUI->_('Reg No') .': '. $supplier_arr['sales_owner_reg_no'] .'</p>';
    $supplier .= '<p>'. $AppUI->_('GST Reg No') .': '. $supplier_arr['sales_owner_gst_reg_no'] .'</p>';

    $quotation_status = '<p><font>Status: </font> '. $quotation_stt_dropdown .'</p>';
    $quotation_info = '<p>Date: <input type="text" size="8" class="text" name="quotation_date_display" id="quotation_date_display" value="'. $date_display .'">';
    $quotation_info .= '<input type="hidden" name="quotation_date" id="quotation_date" value="'. $date_hidden .'">';
    $quotation_info .= $quotation_status;
    $quotation_info .= '<p>Quotation No: <input type="text" class="text" name="quotation_no" id="quotation_no" value="'. $quotation_no .'" onchange="count_change();">';
    $quotation_info .= '<input type="hidden" name="count_click" id="count_click" value="" />';
    $quotation_info .= '<input type="hidden" name="quotation_template" id="quotation_template" value="'.$template.'" />';
    $quotation_info .= '<p>Quotation Rev: <input type="text" class="text" readonly="true" name="quotation_revision" id="quotation_revision" value="'. $quotation_rev .'">';
    
    $div_supplier = $AppUI ->createBlock('div_logo_supplier', $supplier, '');
    $div_quotation_info = $AppUI ->createBlock('div_quotation_info', $quotation_info, '');
    //$div_quotation_status = $AppUI ->createBlock('div_quotation_status', $quotation_status, '');

    $div_logo_left = $AppUI ->createBlock('div_logo_left', $img , 'style="float: left; width: 550px; height:170px;"');
 
    $div_logo_right = $AppUI ->createBlock('div_logo_right', $div_supplier . $div_quotation_info , 'style="float: right; width: 250px;"');

    echo '<div id="div_info_1">' . $div_logo_left . $div_logo_right . '</div>';
    
 // Doan nay cho address (Anhnn update)   
$address_arr = $CSalesManager->get_list_address($client_id);
    if (count($address_arr) > 0) {
        $address_option = '';
        foreach ($address_arr as $address) {
            $selected = '';
            if ($address_id == $address['address_id'])
                $selected = 'selected="selected"';
            $address_option .= '<option value="'. $address['address_id'] .'" '. $selected .'>'. $address['address_street_address_1'] .'</option>';
        }
    }
    $dropdown_address = '<select class="text" name="address_id" id="address_id" onchange="load_client_address(this.value);">'. $address_option .'</select>';
    
    // Doan nay cho companies (Anhnn update)
    $rows = $CSalesManager->get_list_companies();
    //$client_option = '';
    $client_option = '<option value="">-- Select --</option>';
    foreach ($rows as $row) {
        $selected = '';
        if ($client_id == $row['company_id'])
            $selected = 'selected="selected"';
        $client_option .= '<option value="'. $row['company_id'] .'" '. $selected .'>'. $row['company_name'] .'</option>';
    }
    $dropdown_client = '<select class="text" name="customer_id" id="customer_id" onchange="load_address(this.value);">'. $client_option .'</select>';
    
//load ra phone, fax, email cua customer (Anhnn)
    $client_address =  $CSalesManager->get_address_by_id($address_id);
    if (count($client_address) > 0) {
        $option = $client_address['address_phone_1'] .', '. $client_address['address_fax'] .', '. $client_address['address_email'];
    }
        $address_other = _get_client_address();
        //$address_print .= '<p name="client_address" id="client_address">'.$address_other.'</p>';
        $address_print .= '<p name="client_address" id="client_address">'.$option.'</p>';
    
    $client = '<p><b>Bill To: '. $dropdown_client .'</b></p>';
    $client .= '<p>Address: '.$dropdown_address.'</p>';
    $client .= $address_print;       
    
    // Doan nay cho attention 
    $attention_arr = $CSalesManager->get_list_attention($client_id);
    if (count($attention_arr) > 0) {
        $attention_option = '';
        foreach ($attention_arr as $attention) {
            $selected = '';
            if ($attention_id == $attention['contact_id'])
                $selected = 'selected="selected"';
            $attention_option .= '<option value="'. $attention['contact_id'] .'" '. $selected .'>'. $attention['contact_first_name'] .' '. $attention['contact_last_name'] .'</option>';
        }
    }
    $dropdown_attention = '<select class="text" name="attention_id" id="attention_id">'. $attention_option .'</select>';
    $cash = '<p>Attention: '. $dropdown_attention .'</p>';

    $div_2_client = $AppUI ->createBlock('div_2_client', $client, '');
    $div_2_cash = $AppUI ->createBlock('div_2_cash', $cash, '');

    $sales_contact = '<p><b>Sales Contact</b></p>';
    $sales_contact .= '<p>Name: <input type="text" class="text" name="quotation_sale_person" id="quotation_sale_person" value="'.$sale_person_name.'" onkeyup="show_save(\'quotation_sale_person\');">';
    $sales_contact .= '<p>Email: <input type="text" class="text" name="quotation_sale_person_email" id="quotation_sale_person_email" value="'. $sale_person_email .'">';
    $sales_contact .= '<p>Phone: <input type="text" class="text" name="quotation_sale_person_phone" id="quotation_sale_person_phone" value="'. $sale_person_phone .'">';

    $div_2_left = $AppUI ->createBlock('div_2_left', $div_2_client . $div_2_cash, 'style="float: left; width: 550px;"');
    $div_2_right = $AppUI ->createBlock('div_2_right', $sales_contact, 'style="float: right; width: 250px;"');

    echo '<div id="div_info_2">' . $div_2_left . $div_2_right . '</div>';   
}

function  _get_client_address(){
    global $CSalesManager;   
    $client_address =  $CSalesManager->get_address_by_id($_POST['address_id']);
    if (count($client_address) > 0) {
        $option = $client_address['address_phone_1'] .', '. $client_address['address_fax'] .', '. $client_address['address_email'];
    }
    echo $option;
}

function show_html_table($quotation_id = 0, $quotation_revision_id = 0) {

    global $AppUI, $CSalesManager;
    
    $status = $_POST['status_rev'];

    $quotation_item_arr = array();
    if ($quotation_id != 0 && $quotation_revision_id != 0 ) { // neu hanh dong la view mot quotation revision da co trong he thong
        
        global $CQuotationManager;
        
        $quotation_item_arr = $CQuotationManager->get_db_quotation_item($quotation_id, $quotation_revision_id);
    }
                
        echo '<div id="jdatatable_container_detail_quotation_table2" style="width: 100%; padding-bottom: 15px">';
        echo '<table cellspacing="1" cellpadding="2" style="clear: both; width: 100%" id="detail_quotation_table" class="tbl">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="check_all(\'item_select_all\', \'item_check_list\');" id="item_select_all" name="item_select_all"></th>
                        <th>#</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>';
                
            if (isset($quotation_item_arr) && count($quotation_item_arr) > 0) {
                $i = 1;
                foreach ($quotation_item_arr as $quotation_item) {
                    if ($quotation_item['quotation_item_type'] == 0) {
                        $item = $quotation_item['quotation_item'];
                    } elseif ($quotation_item['quotation_item_type'] == 1) {
                        $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                    } elseif ($quotation_item['quotation_item_type'] == 2) {
                        $item = 'Ipad<br><font color="#AAAAAA">description</font>';
                    } else {
                        $item = '<font color="red">Item type not found.</font>';
                    }
                    $item_id = $quotation_item['quotation_item_id'];
                    echo '
                        <tr valign="top" id="row_item_'. $item_id .'">
                            <td valign="top" width="5%" align="center" id="">
                                <input type="checkbox" value="'. $item_id .'" name="item_check_list" id="item_check_list">
                                <a class="icon-edit icon-all" onclick="edit_inline('. $item_id .'); return false;" href="#" title="Inline Edit" style="margin-right:0px"></a></td>
                            <td valign="top" width="4%" align="center" id="stt_'. $item_id .'">'. $i .'</td>
                            <td valign="top" width="25%" id="item_'. $item_id .'">'. $item .'</td>
                            <td valign="top" width="10%" align="right" id="quantity_'. $item_id .'">'. $quotation_item['quotation_item_quantity'] .'</td>
                            <td valign="top" width="10%" align="right" id="price_'. $item_id .'">'. $quotation_item['quotation_item_price'] .'</td>
                            <td valign="top" width="10%" align="right" id="discount_'. $item_id .'">'. $quotation_item['quotation_item_discount'] .'%</td>
                            <td valign="top" width="10%" align="right" id="total_'. $item_id .'">'. '$'. $CSalesManager->calculate_total_item($quotation_item['quotation_item_price'], $quotation_item['quotation_item_quantity'], $quotation_item['quotation_item_discount']) .'</td>
                        </tr>';
                    $i++;
                }
            }
                echo '</tbody>
            </table></div><br/>';
        
//        $delete_id = '<a class="icon-delete icon-all" onclick="delete_quotation_item('. $quotation_id .', '. $quotation_revision_id .', \''.$status.'\'); return false;" href="#">Delete</a>';
        //$edit_id = '<a class="icon-edit" onclick="edit_quotation_item('. $quotation_id .', '. $quotation_revision_id .', \''.$status.'\'); return false;" href="#">Edit</a>';
        //$edit_id = '<a class="icon-edit" onclick="edit_all(); return false;" href="#">Edit</a>';
//        $add_id = '<a class="icon-add icon-all" onclick="add_inline_item(); return false;" href="#">Add item</a>';
        
        echo $block_del = $AppUI ->createBlock('del_block', $delete_id . $edit_id . $add_id, 'style="text-align: left;"');
   // }
    
}

function _form_total_and_note($quotation_id, $quotation_revision_id) {
    global $AppUI;
     $status = $_POST['status_rev'];
    
    $currency_symb = '$'; $total_paid_and_tax = 0;
    
    require_once (DP_BASE_DIR."/modules/sales/CTax.php");
    $CTax = new CTax();
    $tax_arr = $CTax->list_tax();

    if ($quotation_id != 0 && $quotation_revision_id != 0 && $status = 'update') {
        
        global $CQuotationManager;
        $quotation_rev_details_arr = $CQuotationManager->get_db_quotation_revsion($quotation_revision_id);

        if (count($quotation_rev_details_arr) > 0) {
            $quotation_revision = $quotation_rev_details_arr[0]['quotation_revision'];
            $note_area = $quotation_rev_details_arr[0]['quotation_revision_notes'];
            $term_area = $quotation_rev_details_arr[0]['quotation_revision_term_condition_contents'];
            $tax_id = $quotation_rev_details_arr[0]['quotation_revision_tax'];

        }
        
        // Doan xu ly lay ra payment cho total
        require_once (DP_BASE_DIR."/modules/sales/CPaymentManager.php");
        $CPaymentManager = new CPaymentManager();
        $payment_arr = $CPaymentManager->list_db_payment($quotation_revision_id);
        $tr_paid = '';
        if (count($payment_arr) > 0) {
            foreach ($payment_arr as $payment) {
                $tr_paid .= 
                    '<tr>
                        <td class="td_left">Amount Paid :</td>
                        <td class="td_right">'. $currency_symb .''. $payment['payment_amount'] .'</td>
                    </tr>';
                
                $total_paid_and_tax += intval($payment['payment_amount']);
            }
        }
        
        $total_item_show = $CQuotationManager->get_quotation_item_total($quotation_id, $quotation_revision_id);
        $revison_lastest = $CQuotationManager->get_latest_quotation_revision($quotation_id);
        $AppUI->addJSScript('$(\'#quotation_revision\').val(\''.$quotation_revision.'\'); $(\'#quotation_rev_label\').append(\' <font color="red">'.$quotation_revision.'</font><input type="hidden" name="revision_lastest" id="revision_lastest" value="'.$revison_lastest[0]['quotation_revision'].'" />\');');
    }

    $tax_option = '<option vaue="">---</option>';
    if (count($tax_arr) > 0) {
        foreach ($tax_arr as $tax) {
            $selected = '';
            if ($tax_id) { // neu tinh trang la update
                if ($tax['tax_id'] == $tax_id) {
                    $selected = 'selected="selected"';
                    $tax_value = $tax['tax_rate'];
                }
            } else { // neu tinh trang la add lay ra ta default
                if ($tax['tax_default'] == 1) {
                    $selected = 'selected="selected"';
                    $tax_value = $tax['tax_rate'];
                }
            }

            $tax_option .= '<option value="'. $tax['tax_id'] .'" '. $selected .'>'. $tax['tax_rate'] .'</option>';
        }
    }
    
    if ($tax_id) {
        $caculating_tax = (intval($total_item_show) * intval($tax_value) / 100);
        $total_item_tax_show = $currency_symb . $caculating_tax;
        $total_paid_and_tax += $caculating_tax;
    }

    $tax_select = '<select name="quotation_revision_tax" id="quotation_revision_tax" class="text">'. $tax_option .'</select>';

    
    
    
    $table = '<table boder="0" width="100%" class="tbl_total">
        <tr>
            <td class="td_left">'. $AppUI->_('Total') .' :</td>
            <td class="td_right">'. $currency_symb .''. $total_item_show .'</td>
        </tr>
        <tr>
            <td class="td_left">'. $AppUI->_('Tax') .' @ '. $tax_select .'% :</td>
            <td class="td_right">' . $total_item_tax_show . '</td>
        </tr>
        ' . $tr_paid . '
        <tr>
            <td class="td_left">'. $AppUI->_('Amount Due') .' :</td>
            <td class="td_right">'. $currency_symb .''. ($total_item_show - $total_paid_and_tax) .'</td>
        </tr>

        </table>';

    echo '<div id="div_4">' . $table . '</div>';

     $textarea = '<textarea id="quotation_revision_notes" class="textArea" style="width: 350; height: 80;" name="quotation_revision_notes">'. $note_area .'</textarea>';
     $textarea1 = '<textarea id="quotation_revision_term_condition_contents" class="textArea" style="width: 350; height: 80;" name="quotation_revision_term_condition_contents">'. $term_area .'</textarea>';


    $note_block = $AppUI ->createBlock('div_notes', 'Notes: <br/>'. $textarea, 'style="float: left;"');
    
    $terms_block = $AppUI ->createBlock('div_terms', 'Terms and Conditions: <br/>'. $textarea1, 'style="float: right;"');
    
    $save_block = '<button id="btn-save-all" class="ui-button ui-state-default ui-corner-all" onclick="save_all_quotation('. $quotation_id .', '. $quotation_revision_id .', \''. $status .'\'); return false;">Save All</button>&nbsp';

    echo '<div id="div_5">' . $note_block . $terms_block .'</div>';
    echo $AppUI ->createBlock('btt_block', $save_block , 'style="text-align: center; float: right"');
    
}
?>
