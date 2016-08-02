<?php 

if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

global $AppUI;
?>
<script language='javascript'>

        function save_tax() {
                var tax_name = $('#tax_name').val();
                var tax_rate = $('#tax_rate').val();
                if (isFloat(tax_rate)) {
                    _disable_btn('btn-add-tax'); // disable button
                    $.post('?m=sales&a=configure&c=do_add_tax&suppressHeaders=true', { tax_name: tax_name, tax_rate: tax_rate },
                            function(data) {
                                if (data == 'success')  {
                                    load_tax();
                                } else {
                                    //alert('loi trong qua trinh add.');
                                    load_tax();
                                }
                            }
                    );
                } else {
                    alert('Tax rate should be float.');
                }

        }

        function check_all(check_all_id, check_element_name) {
            var selectAll = document.getElementById(check_all_id);
            var inputs = document.getElementsByName(check_element_name);

            if(selectAll.checked == true) {
                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].type == 'checkbox') {
                            inputs[i].checked = true;
                    }
                }
            } else {
                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].type == 'checkbox') {
                            inputs[i].checked = false;
                    }
                }
            }
        }
        
        function set_tax_default() {
                var tax_str_id = get_taxID_checked('check_list', 'tax_id');
                if (tax_str_id != '') {
                    var tax_split = tax_str_id.split('&tax_id[]=');
                    if ((parseInt(tax_split.length) - 1) == 1) {
                            $.post('?m=sales&a=configure&c=_do_update_tax_default'+ tax_str_id +'&suppressHeaders=true', {  },
                                function(data) {
                                    if (data.status == 'Success') {
                                        load_tax();
                                    } else if (data.status == "Failure") {
                                        alert(data.message);
                                    }
                            }, "json");
                    } else {
                        alert('<?php echo $AppUI->_('Ban chi duoc chon 1 tax.'); ?>');
                    }
                } else {
                    alert('<?php echo $AppUI->_('Please select at least one Tax to setup'); ?>');
                }
        }
        
        function load_tax() {
            $('#div_tax_cofig').load('?m=sales&a=configure&c=show_html_table&suppressHeaders=true');
        }

        function isFloat(val) {
            if(!val || (typeof val != 'string' || val.constructor != String)) {
                    return(false);
            }
            var isNumber = !isNaN(new Number(val));
            if(isNumber) {
                    return(true);
            } else {
                    return(false);
            }
        }
        
        function get_taxID_checked(id_check, str_check) {
            var inputs = document.getElementsByName(id_check);
            var invoice_id = '';
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                    invoice_id += '&'+str_check+'[]='+inputs[i].value;
                }
            }
            return invoice_id;
        }
        
        function delete_tax() {
            var tax_str_id = get_taxID_checked('check_list', 'tax_id');
            if (tax_str_id != '') {
                if (confirm('<?php echo $AppUI->_('Are you sure delete record(s)'); ?>')) {
                    $.post('?m=sales&a=configure&c=_do_delete_tax'+ tax_str_id +'&suppressHeaders=true', {  },
                        function(data) {
                            if (data.status == 'Success') {
                                load_tax();
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one Tax to delete'); ?>');
            }
        }
        
        function remove_logo(url) {
                if (confirm('<?php echo $AppUI->_('Are you sure delete this logo'); ?>')) {
                    $.post('?m=sales&a=configure&c=_do_delete_logo&suppressHeaders=true', { url: url },
                        function(data) {
                            if (data.status == 'Success') {
                                $('#div_logo_cofig').load('?m=sales&a=configure&c=_vw_upload_logo_form&suppressHeaders=true');
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
        }
        
        function validate_form() {
            if ($('#logo').val() == '') {
                alert('Please choose a file.')
                return false;
            }
            return true;
        }
        
</script>
