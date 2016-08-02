
<?php

if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

$a = $_GET['a'];

?>
<script language="javascript">
            $('#receipt_search').select2();

    //$().ready(function() {
        var a = '<?php echo $a ?>';
        if (a == 'vw_invoice') {
            var id_main = 'div#detail_payment_inside';
        }
        else if (a == 'vw_payment') {
            var id_main = 'div#detail_payment';
        }  
    //});
    $(document).ready(function() {
                loadJQueryCalendar('payment_date', 'payment_date_hidden', 'dd/M/yy', 'yy-mm-dd');
                
                $('#payment_customer').chosen();
                $('.chzn-container').css({'top':'7'});
//                $('#receipt_search').load('?m=sales&a=vw_payment&c=_get_receipt_select&suppressHeaders=true');

	});
    function load_invoice_by_customer(customer_id) {
       $('#payment_invoice').load('?m=sales&a=vw_payment&c=_get_invoice_select&suppressHeaders=true', { customer_id: customer_id });
       $('#receipt_search').load('?m=sales&a=vw_payment&c=_get_receipt_select&suppressHeaders=true', { customer_id: customer_id });
    }
    function load_reipt(invoice_id) {
      var customer_id=$('#payment_customer').val();
       $('#receipt_search').load('?m=sales&a=vw_payment&c=_get_receipt_select&suppressHeaders=true', { customer_id: customer_id,invoice_id:invoice_id });
    }
   
    function load_payment(payment_invoice, payment_customer) {
        if (payment_invoice == undefined && payment_customer == undefined) {
            var payment_customer = $('#payment_customer').val();
            var payment_invoice = $('#payment_invoice').val();
            
        }
        var status_invoice_id = $('#status_invoice_id').val();
        var receipt_search = $('#receipt_search').val();
        
        if (payment_customer != '') {
            $('#detail_payment').html('Loading...');
            $('#detail_payment').load('?m=sales&a=vw_payment_new&c=vw_payment_detail&suppressHeaders=true', { payment_invoice: payment_invoice, payment_customer: payment_customer,receipt_search:receipt_search,status_invoice_id:status_invoice_id });
        } else {
            alert('Please choose Customer for Payment');
        }
    }
    /*
     * add new payment update by dungnv
     */
    function add_new_payment() {
        $('#new_payment').html('Loading...');
        $('#new_payment').load('?m=sales&a=vw_payment&c=vw_add_payment&suppressHeaders=true');
        
    }
    function add_new_payment_new(customer_id,creditNote_id,total_amount){
        
        $('#new_payment_new').html('Loading...');
        $('#new_payment_new').load('?m=sales&a=vw_payment_new&c=vw_add_payment_new&suppressHeaders=true',{customer_id:customer_id,creditNote_id:creditNote_id,total_amount:total_amount});
    }
    
    function add_row_payment(customer_id, invoice_revision_id, invoice_id) {
        var id = invoice_revision_id; 
        <?php 
            $PaymentMethods = dPgetSysVal('PaymentMethods');
            $option = '';
            foreach ($PaymentMethods as $key => $value) {
                $option .= '<option value="'. $key .'">'. $value .'</option>';
            }
        ?>
        var date_hidden = '';
        var date_display = '';
        var option = '<?php echo $option; ?>';
        var select = '<select class="text" name="payment_method" id="payment_method_'+ id +'">'+ option +'</select>';
        var tr = '';
        tr += '<td><a href="#" onclick="remove_payment_row_add('+ id +'); return false;">Hide</a></td>';
        tr += '<td align="right" valign="top">';
            tr += '<input class="text" type="text" name="payment_amount" id="payment_amount_'+ id +'" size="9"/></td>';
        tr += '<td valign="top">';
            tr += select +'</td>';
        tr += '<td valign="top">';
            tr += '<input class="text" type="text" name="payment_date_display" id="payment_date_display_'+ id +'" value="'+ date_display +'" size="8"/></td>';
            tr += '<input type="hidden" name="payment_date" id="payment_date_'+ id +'" value="'+ date_hidden +'">';
        tr += '<td valign="top">';
            tr += '<textarea class="textArea" name="payment_notes" id="payment_notes_'+ id +'"></textarea>';
            tr += '<a href="#" onclick="save_payment('+ customer_id +', '+ id +', '+ invoice_id +'); return false;"><img border="0" src="images/icons/save.png"></a>';
            tr += '</td>';
        
        $(id_main +' #tr_invoice_'+id).html(tr);
        loadJQueryCalendar('payment_date_display_'+ id, 'payment_date_'+ id, 'dd/mm/yy', 'yy-mm-dd');
    }
    
    function add_row_payment_schedule(customer_id, invoice_revision_id) {
        var id = invoice_revision_id; 
        var tr = '';
        tr += '<td><a href="#" onclick="remove_payment_schedule_row_add('+ id +'); return false;">Hide</a></td>';
        tr += '<td align="right" valign="top">';
            tr += '<input class="text" type="text" name="payment_schedule_paid" id="payment_schedule_paid_'+ id +'" size="9"/></td>';
        tr += '<td valign="top">';
            tr += '<input class="text" type="text" name="payment_schedule_paid_date_display" id="payment_schedule_paid_date_display_'+ id +'" size="8"/></td>';
            tr += '<input type="hidden" name="payment_schedule_paid_date" id="payment_schedule_paid_date_'+ id +'" value="">';
        tr += '<td valign="top">';
            tr += '<textarea class="textArea" name="payment_schedule_notes" id="payment_schedule_notes_'+ id +'"></textarea>';
            tr += '<a href="#" onclick="save_payment_schedule('+ customer_id +', '+ id +'); return false;"><img border="0" src="images/icons/save.png"></a>';
            tr += '</td>';
        $(id_main +' #tr_invoice_schedule_'+id).html(tr);
        loadJQueryCalendar('payment_schedule_paid_date_display_'+ id, 'payment_schedule_paid_date_'+ id, 'dd/mm/yy', 'yy-mm-dd');
    }

    function remove_payment_row_add(invoice_revision_id) {
        $(id_main +' #tr_invoice_'+invoice_revision_id).html('');
    }
        
    function remove_payment_schedule_row_add(invoice_revision_id) {
        $(id_main +' #tr_invoice_schedule_'+invoice_revision_id).html('');
    }
    
    function save_payment(customer_id, invoice_revision_id, invoice_id) {
        var id = invoice_revision_id;
        var payment_customer = $(id_main +'#payment_customer').val();
        var payment_invoice = $('#invoce_id_payment').val();
        var payment_amount = $(id_main +' #payment_amount_'+id).val();
        var payment_method = $(id_main +' #payment_method_'+id).val();
        var payment_date = $(id_main +' #payment_date_'+id).val();
        var payment_notes = $(id_main +' #payment_notes_'+id).val();
            $.post('?m=sales&a=vw_payment&c=_do_add_payment&suppressHeaders=true', { invoice_revision_id: id, payment_amount: payment_amount, payment_method: payment_method, payment_date: payment_date, payment_notes: payment_notes },
                function(data) {
                    if (data.status == 'Success') {
                        //load_tbl_payment(customer_id);
                        load_tr_payment(customer_id, id);
                        load_payment(invoice_id, customer_id);
                    } else if (data.status == "Failure") {
                        alert(data.message);
                    }
            }, "json"); 
    }
    
    function save_payment_schedule(customer_id, invoice_revision_id) {
        var id = invoice_revision_id;
        
        var payment_schedule_paid = $(id_main +' #payment_schedule_paid_'+id).val();
        var payment_schedule_paid_date = $(id_main +' #payment_schedule_paid_date_'+id).val();
        var payment_schedule_notes = $(id_main +' #payment_schedule_notes_'+id).val();
            
            $.post('?m=sales&a=vw_payment&c=_do_add_payment_schedule&suppressHeaders=true', { invoice_revision_id: id, payment_schedule_paid: payment_schedule_paid, payment_schedule_paid_date: payment_schedule_paid_date, payment_schedule_notes: payment_schedule_notes },
                function(data) {
                    if (data.status == 'Success') {
                        load_tbl_payment_schedule(customer_id);
                        //load_tr_payment_schedule(customer_id, id);
                    } else if (data.status == "Failure") {
                        alert(data.message);
                    }
            }, "json");
        
    }
    
    function load_tr_payment(customer_id, invoice_revision_id) {
        $(id_main +' #show_tr_payment_'+invoice_revision_id).html('<?php echo $AppUI->_('Loading...');?>');
	$(id_main +' #show_tr_payment_'+invoice_revision_id).load('?m=sales&a=vw_payment&c=_load_vw_tr_payment&suppressHeaders=true', { customer_id: customer_id, invoice_revision_id_lastest: invoice_revision_id });
    }
    
    function load_tr_payment_schedule(customer_id, invoice_revision_id) {
        $(id_main +' #show_tr_payment_schedule_'+invoice_revision_id).html('<?php echo $AppUI->_('Loading...');?>');
	$(id_main +' #show_tr_payment_schedule_'+invoice_revision_id).load('?m=sales&a=vw_payment&c=_load_vw_tr_payment_schedule&suppressHeaders=true', { customer_id: customer_id, invoice_revision_id_lastest: invoice_revision_id });
    }
    
    function load_tbl_payment(customer_id, invoice_revision_id) {
        $(id_main +' #div_tbl_payment_'+customer_id).html('<?php echo $AppUI->_('Loading...');?>');
        $(id_main +' #div_tbl_payment_'+customer_id).load('?m=sales&a=vw_payment&c=_load_vw_tbl_payment&suppressHeaders=true', { customer_id: customer_id, invoice_revision_id: invoice_revision_id });
    }
    
    function load_tbl_payment_schedule(customer_id) {
        $(id_main +' #div_tbl_payment_schedule_'+customer_id).html('<?php echo $AppUI->_('Loading...');?>');
	$(id_main +' #div_tbl_payment_schedule_'+customer_id).load('?m=sales&a=vw_payment&c=_load_vw_tbl_payment_schedule&suppressHeaders=true', { customer_id: customer_id });
    }
    
    function create_payment_receipt_pdf(customer_id) {
                var payment_str_id = get_ID_checked('payment_check_list_'+customer_id, 'payment_id');
                //alert(payment_str_id)
                if (payment_str_id != '') {
                    var payment_split = payment_str_id.split('&payment_id[]=');
                    if ((parseInt(payment_split.length) - 1) == 1) {
                        if (confirm('<?php echo $AppUI->_('Are you sure print to pdf this payment?'); ?>')) {
                            window.open('?m=sales&a=vw_payment&c=_do_print_payment&suppressHeaders=true&customer_id='+customer_id + payment_str_id,'_Blank','');
                        }
                    } else {
                        alert('<?php echo $AppUI->_('Hien tai he thong chi cho phep chon 1 payment de in PDF'); ?>');
                    }
                } else {
                    alert('<?php echo $AppUI->_('Please select at least one payment to print'); ?>');
                }
    }
    function print_receipt(customer_id,payment_id){
        window.open('?m=sales&a=vw_payment&c=_do_print_payment&suppressHeaders=true&customer_id='+customer_id +'&payment_id='+payment_id,'_Blank','');
    }
    
    function send_mail_payment_receipt(customer_id) {
                var payment_str_id = get_ID_checked('payment_check_list_'+customer_id, 'payment_id');
                if (payment_str_id != '') {
                    if (confirm('<?php echo $AppUI->_('Are you sure send email(s) to customers?'); ?>')) {
                        $.post('?m=sales&a=vw_payment&c=_do_send_email_payment'+ payment_str_id +'&suppressHeaders=true', { customer_id: customer_id },
                            function(data) {
                                alert(data.message);
                        }, "json");
                    }
                } else {
                    alert('<?php echo $AppUI->_('Please select at least one Payment to send email'); ?>');
                }
    }
    
    function delete_payment(customer_id) {
        var invoice_id = $('#payment_invoice').val();
        
        var payment_customer = $('#payment_customer').val();
        if(payment_customer==""){
            var customer_id_load=payment_customer;
        }
        else{
            customer_id_load=customer_id;
        }
        var payment_detail_str_id = get_ID_checked('payment_check_list_'+customer_id, 'payment_detail_id');
        if (payment_detail_str_id != '') {
            if (confirm('<?php echo $AppUI->_('Are you sure delete record(s)'); ?>')) {
                $.post('?m=sales&a=vw_payment&c=_do_remove_payment_detail'+ payment_detail_str_id +'&suppressHeaders=true', {  },
                function(data) {
                    if (data.status == 'Success') {
                        load_payment(invoice_id, customer_id_load);
                        $.post('?m=sales&a=vw_invoice&c=_do_update_status_invoice1&suppressHeaders=true&invoice_id='+invoice_id, {update_payment:"delete"});
                    } else if (data.status == "Failure") {
                        alert(data.message);
                    }
                }, "json");
            }
         } else {
                alert('<?php echo $AppUI->_('Please select at least one Payment to delete'); ?>');
         }
    }
    
    function delete_payment_schedule(customer_id) {
            var payment_schedule_str_id = get_ID_checked('payment_schedule_check_list_'+customer_id, 'payment_schedule_id');
            if (payment_schedule_str_id != '') {
                if (confirm('<?php echo $AppUI->_('Are you sure delete record(s)'); ?>')) {
                    $.post('?m=sales&a=vw_payment&c=_do_remove_payment_schedule'+ payment_schedule_str_id +'&suppressHeaders=true', {  },
                        function(data) {
                            if (data.status == 'Success') {
                                load_tbl_payment_schedule(customer_id);
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one Payment schedule to delete'); ?>');
            }
    }
    
    function save_payment_add() {
//        var customer_id = $('#customer_id').val();
        var invoice_id = $('#invoice_id').val();
        
        var element_id = invoice_id.split(",");
        invoice = element_id[0];    
        customer_id = element_id[1];
        ivoice_rev_id = element_id[2];
        total_invoice = element_id[3];
        var payment_date = $('#payment_date').val();
        var payment_method = $('#payment_method').val();
        var payment_notes = $('#payment_notes').val();
        var payment_amount = $('#payment_amount').val();
        var payment_cheque_nos = $('#payment_receipt_no').val();
        var payment_description = $('#payment_description').val();
        if (invoice == '') {
            alert('Please choose invoice.');
        } else if (payment_amount == '') {
            alert('Please enter payment amount.');
        } else if(isNaN(payment_amount) == true) {
            alert('Payment amount invalid.');
        } else if(parseInt(payment_amount) > total_invoice) {
            alert('The Payment Amount cannot exceed the Invoice Balance.');
        } else if(parseInt(payment_amount) <= 0){
            alert('Please enter payment amount >0');
        }
        else {
            $(".loading_active").fadeIn();
            $.post('?m=sales&a=vw_payment&c=_do_add_payment&suppressHeaders=true', {invoice_id: invoice, customer_id: customer_id, invoice_revision_id: ivoice_rev_id, payment_amount: payment_amount, payment_date: payment_date, payment_method: payment_method, payment_notes: payment_notes,payment_cheque_nos:payment_cheque_nos,payment_description:payment_description},
                    function(data) {
                        if (data.status == 'Success') {
                            $('#new_payment').html('');
                            load_payment(invoice, customer_id);
                            //load_tr_payment_schedule(customer_id, id);
                        } else if (data.status == "Failure") {
                            alert(data.message);
                        }
                }, "json");
        }
    }
    function back_list(){
        $('#new_payment_new').html('');
    }
    
    function popupSendEmailReceipt(customer_id){
        var id = 'div-popup';
        var attention_id = $('#attention_id').val();
        var payment_str_id = get_ID_checked('payment_check_list_'+customer_id, 'payment_id');
        var payment_split = payment_str_id.split('&payment_id[]=');
        if ((parseInt(payment_split.length) - 1) == 1){
            $('#'+id).html("Loading ...");
            $('#'+id).load('?m=sales&a=vw_payment_new&c=_form_send_mail_receipt'+payment_str_id+'&suppressHeaders=true',{customer_id:customer_id}).dialog({
            resizable: false,
            modal: true,
            title: '<?php echo $AppUI->_('Send email'); ?>',
            width: 700,
            maxheight: 400,
            close: function(ev,ui){
                $('#'+id).dialog('destroy');
            }
            });
            $('#'+id).dialog('open');
        }else{
            alert('<?php echo $AppUI->_('Please select at least one payment to Email'); ?>');
        }
    }
    
    function setSendButtonRe(customer_id, payment_detail_id)
             {
               var id = 'div-popup';
               if (confirm('<?php echo $AppUI->_('Are you sure to send email(s) to customers?'); ?>')) {
                  var sender = $('#sender').val();
                  var reciver = $('#reciver').val();
                  var subject = $('#subject').val();
                  var content = $('#content').val();
                        $.post('?m=sales&a=vw_payment_new&c=_do_send_email_payment&suppressHeaders=true', {customer_id: customer_id, payment_detail_id: payment_detail_id, sender: sender, reciver: reciver, subject: subject, content: content },
                            function(data) {
                                if (data.status == 'Success'){
                                    alert(data.message);
                                    $('#'+id).dialog('close');
                                }
                            else if (data.status == 'Failure') {
                                  alert(data.message);
                            }
                                
                        }, "json");
                       // window.location.href = 'index.php?m=sales&a=vw_invoice&c=_do_send_email_invoice&suppressHeaders=true&invoice_id='+ invoice_id +'&invoice_revision_id='+ invoice_revision_id;
                    }
            }
    function editDate(id,date_id,date_fomat,date_value){
        var str = '<input value="'+date_fomat+'" type="text" readonly="readonly" class="text" style="margin-bottom:2px;" size="10" id="fiter_'+date_id+'">';
        str +='<input value="'+date_value+'" type="hidden" class="text" style="margin-bottom:2px;" size="10" id="hdd_fiter_'+date_id+'">'; 
        str += '<input type="button" value="Save" class="ui-button ui-state-default ui-corner-all" onclick="saveDate('+id+',\''+date_id+'\')" />&nbsp;&nbsp;';
        str += '<input type="button" value="Cancel" class="ui-button ui-state-default ui-corner-all" onclick="cancelDate('+id+',\''+date_id+'\')" />';
        $('#'+date_id).html(str);
        loadJQueryCalendar('fiter_'+date_id, 'hdd_fiter_'+date_id, 'dd/mm/yy', 'yy-mm-dd');
        $('#'+date_id).attr('onclick','');
        
    }
    function saveDate(id,date_id){
        
        var new_payment_date = $('#hdd_fiter_'+date_id).val();
        var new_payment_date_format = $('#fiter_'+date_id).val();
        
        
        $.post('?m=sales&a=vw_payment&c=_do_update_payment_field&suppressHeaders=true',{id:id,field_name:"payment_date",value:new_payment_date},
        function(data){
            if(data.status=='Success')
                $('#'+date_id).html(new_payment_date_format);
                $('#'+date_id).unbind('click').bind('click',(function(event){
                    var tgt=$(event.target);
                    if(tgt.is('div'))
                    $('#'+date_id).attr('onclick',editDate(id,date_id,new_payment_date_format,new_payment_date));
                }));
        }
            
    ,'json');
    }
    function cancelDate(id,date_id){
            var new_payment_date = $('#hdd_fiter_'+date_id).val();
            var new_payment_date_format = $('#fiter_'+date_id).val();
                $('#'+date_id).html(new_payment_date_format);
                $('#'+date_id).unbind('click').bind('click',(function(event){
                    var tgt=$(event.target);
                    if(tgt.is('div'))
                    $('#'+date_id).attr('onclick',editDate(id,date_id,new_payment_date_format,new_payment_date));
                }));
    }

</script>
