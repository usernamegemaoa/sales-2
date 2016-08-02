<script>
    
    credit_item_id_arr = new Array();
    credit_amount_arr = new Array();
    $(document).ready(function(){
        loadJQueryCalendar('credit_date_voucher_dislay','credit_date_voucher','dd/M/yy','yy-mm-dd');
        var oTable = $('#credit_table_item').dataTable();
        $('#credit_customer').select2();
        $('#credit_co').select2();
    });
    function add_line_item_redit(){
        var count_row = $('#count_row_item').val();
        
        var tr='<tr id="row_item_'+count_row+'">';
            tr+='<td align="center" width="6%"><img border="0" style="cursor: pointer" onclick="remove_item_credit('+ count_row +');" src="images/icons/stock_delete-16.png"></td>';
            tr+='<td align="center" width="6%"><input class="text" type="text" name="order_credit[]" id="order_credit_'+count_row+'" value="'+count_row+'" size="4"  /></td>';
            tr+='<td><textarea cols="70" rows="4" name="dis_credit[]" id="dis_credit_'+count_row+'"></textarea></td>';
            tr+='<td align="center"><input class="text" type="text" onchange="load_update_total('+count_row+')" name="amount_credit[]" id="amount_credit_'+count_row+'" size="12" /></td>';
        tr+='</tr>';
        $('#add_line_credit_item').append(tr);
        $('#count_row_item').val(parseInt(count_row)+1);
        credit_item_id_arr.push(count_row);
        //alert(credit_item_id_arr);
    }
    function remove_item_credit(item_id){
        var key = item_id.toString();
        var key = credit_item_id_arr.indexOf(key);
        credit_item_id_arr.splice(key,1);
        $('#row_item_'+item_id).remove();
        load_update_total();
    }
    function load_creditNote(creditNote_id,status){
        $('#div_creditNote').html('Loading...');
        if(creditNote_id==undefined || creditNote_id =="")
            $('#div_creditNote').load('?m=sales&a=vw_creditNote&c=view_creditNote_detail&suppressHeaders=true',{status:status});
        else
            $('#div_creditNote').load('?m=sales&a=vw_creditNote&c=view_creditNote_detail&suppressHeaders=true',{creditNote_id:creditNote_id,status:status});
    }
    function load_address_by_customer(customer_id){
        //alert(customer_id);
        $('#credit_addres').load('?m=sales&a=vw_creditNote&c=get_address_by_customer&suppressHeaders=true',{customer_id:customer_id});
        $('#credit_invoice').load('?m=sales&a=vw_creditNote&c=get_invoice_by_customer&suppressHeaders=true',{customer_id:customer_id});
        $('#credit_attention').load('?m=sales&a=vw_creditNote&c=get_attention_by_customer&suppressHeaders=true',{customer_id:customer_id});
    }
    function load_infoAddress_by_address(address_id){
        $('#credit_tel').load('?m=sales&a=vw_creditNote&c=get_infoAddress_by_address&suppressHeaders=true',{address_id:address_id,info:"tel"});
        $('#credit_fax').load('?m=sales&a=vw_creditNote&c=get_infoAddress_by_address&suppressHeaders=true',{address_id:address_id,info:"fax"});
    }
    function load_date_by_invoice(invoice_id){
        var invoice_arr = $('#credit_invoice').val();
        var element_id = invoice_arr.split(",");
        var invoice_id = element_id[0];
        var invoice_rev_id = element_id[1];
        $('#credit_date_invoice').load('?m=sales&a=vw_creditNote&c=get_date_by_invoice&suppressHeaders=true',{invoice_id:invoice_id});
        $('#view_invoice').load('?m=sales&a=vw_creditNote&c=_do_load_view_invoice&suppressHeaders=true',{invoice_id:invoice_id,invoice_rev_id:invoice_rev_id});
    }
    function save_all_creditNote(creditNote_id,status){
        var customer_id = $('#credit_customer').val();
        var credit_co = $('#credit_co').val();
        var address_id = $('#credit_addres').val();
        var credit_voucher_no = $('#credit_voucher_no').val();
        var credit_date_voucher = $('#credit_date_voucher').val();
        var invoice_arr = $('#credit_invoice').val();
        var attention_id = $('#credit_attention').val();
        var payment_amount = new Array();
        var invoice_rev_id = new Array();
        var element_id = invoice_arr.split(",");
        var invoice_id = element_id[0];
        invoice_rev_id[0] = element_id[1];
        payment_amount[0] = $('#credit_total_hdd').val(); 
        var creditNote_tax = $('#creditNote_tax').val();
        var creditNote_item_arr = $('#frm_item_creditNote').serialize();
        var total_item_credit = $('#total_creditNote').val();
        var cont_row = $('#count_row_item').val();
        var creditNote_tax_edit_value = $('#credit_tax_value').val();
        total_item_credit = parseInt(total_item_credit) + parseInt(cont_row);
        //alert(total_item_credit);
        if(customer_id==""){
            alert("Please provide Customer name.");
            return;
        }
        if(trim(credit_voucher_no)==""){
            alert ('Sorry. This Credit Note No is empty');
            return;
        }
        if(total_item_credit<2){
            alert ("Please enter at  least a Item's detail");
            return;
        }
        var creditNote_item = new Array();
        var creditNote_order = new Array();
        var creditNote_amount = new Array();
        var check_item = 0;
        for( i=1;i<=credit_item_id_arr.length;i++){
            var j = credit_item_id_arr[i-1];
            creditNote_item[i]=$('#dis_credit_'+j).val();
            creditNote_order[i]=$('#order_credit_'+j).val();
            creditNote_amount[i]=$('#amount_credit_'+j).val();
            if(trim(creditNote_item[i])=="" || creditNote_amount[i]=="")
                check_item = 1;
            else if(isNaN(creditNote_amount[i])==true)
                check_item = 2;
            else if(is_int(creditNote_order[i])==false)
                check_item = 3;
        }
//        alert('123');
        if(check_item == 1){
            alert("Please enter Description and Amount");
            return;
        }
        if(check_item == 2){
            alert("Please enter Amount must is number");
            return;
        }
        if(check_item == 3){
            alert("Please enter # must is integer");
            return;
        }
        
        if(check_item==0){
            if(status=="add"){
                $(".loading_active").fadeIn();
                
                $.post('?m=sales&a=vw_creditNote&c=_do_save_creditNote&suppressHeaders=true',{customer_id:customer_id,address_id:address_id,credit_voucher_no:credit_voucher_no,credit_date_voucher:credit_date_voucher,invoice_id:invoice_id,creditNote_tax:creditNote_tax,creditNote_tax_edit_value:creditNote_tax_edit_value,attention_id:attention_id,credit_co:credit_co},
                function(data){
                    if(data.status=="Success"){
                       var credit_note_id = data.credit_note_id;
                       creditNote_item_arr = creditNote_item_arr+"&credit_note_id="+data.credit_note_id;
                       $.post('?m=sales&a=vw_creditNote&c=_do_save_creditNote_item&suppressHeaders=true',creditNote_item_arr,
                        function(data){
                            if(data.status=="Success")
                                if(invoice_id!=""){
                                    $.post('?m=sales&a=vw_payment_new&c=_do_payment_new&suppressHeaders=true',{payment_date: credit_date_voucher,payment_notes:credit_voucher_no,credit_note_id:credit_note_id,payment_method:5},
                                        function(data){
                                            if(data.status=="Success"){
                                                var payment_id = data.payment_id;
                                                $.post('?m=sales&a=vw_payment_new&c=_do_add_paymentDetail_new&suppressHeaders=true',{payment_id:payment_id,payment_amount: payment_amount, payment_invoice_rev_id:invoice_rev_id,count_payment_invoice:1},
                                                    function(data){
                                                        if(data.status=="Success")
                                                               $.post('?m=sales&a=vw_creditNote&c=_do_update_status_creditNote&suppressHeaders=true',{creditNote_id:credit_note_id,credit_note_status:2},
                                                                function(data){
                                                                    $('#div_creditNote').load('?m=sales&a=vw_creditNote&c=view_creditNote_detail&suppressHeaders=true',{creditNote_id:credit_note_id,status:"update"});
                                                                },"json");
                                                    },"json");
                                            }else
                                                alert("Error");
                                        }
                                    ,"json");
                                }
                            else{
                                 $('#div_creditNote').html('Loading...');
                                 $('#div_creditNote').load('?m=sales&a=vw_creditNote&c=view_creditNote_detail&suppressHeaders=true',{creditNote_id:credit_note_id,status:"update"});
                             }
                        },"json");  

                    }
                    else if(data.status=="Exist"){
                        
                        alert(data.masage);
                        $(".loading_active").fadeOut();
                    }
                    else if(data.status=="Failure"){
                        alert("Error");
                        $(".loading_active").fadeOut();
                    }
                },"json");
            }
            else if(status=="update"){
                var sales_attention_id = $('#sales_attention_id').val();
//                $(".loading_active").fadeIn();
                $.post('?m=sales&a=vw_creditNote&c=_do_update_creditNote&suppressHeaders=true',{creditNote_id:creditNote_id,customer_id:customer_id,address_id:address_id,credit_voucher_no:credit_voucher_no,credit_date_voucher:credit_date_voucher,invoice_id:invoice_id,creditNote_tax:creditNote_tax,creditNote_tax_edit_value:creditNote_tax_edit_value,attention_id:attention_id,sales_attention_id:sales_attention_id,credit_co:credit_co},
                function(data){
                    if(data.status=="Success"){
                       creditNote_item_arr = creditNote_item_arr+"&credit_note_id="+creditNote_id;
                       $.post('?m=sales&a=vw_creditNote&c=_do_save_creditNote_item&suppressHeaders=true',creditNote_item_arr,
                        function(data){
                                if(invoice_id!=""){
                                    $.post('?m=sales&a=vw_payment_new&c=_do_payment_new&suppressHeaders=true',{payment_date: credit_date_voucher,payment_notes:credit_voucher_no,credit_note_id:creditNote_id,payment_method:5},
                                        function(data){
                                            if(data.status=="Success"){
                                                var payment_id = data.payment_id;
                                                $.post('?m=sales&a=vw_payment_new&c=_do_add_paymentDetail_new&suppressHeaders=true',{payment_id:payment_id,payment_amount: payment_amount, payment_invoice_rev_id:invoice_rev_id,count_payment_invoice:1},
                                                    function(data){
                                                        if(data.status=="Success")
                                                               $.post('?m=sales&a=vw_creditNote&c=_do_update_status_creditNote&suppressHeaders=true',{creditNote_id:creditNote_id,credit_note_status:2},
                                                                function(data){
                                                                    $('#div_creditNote').html('Loading...');
                                                                    $('#div_creditNote').load('?m=sales&a=vw_creditNote&c=view_creditNote_detail&suppressHeaders=true',{creditNote_id:creditNote_id,status:"update"});
                                                                },"json");
                                                    },"json");
                                            }
                                            else
                                            {
                                                $('#div_creditNote').html('Loading...');
                                                $('#div_creditNote').load('?m=sales&a=vw_creditNote&c=view_creditNote_detail&suppressHeaders=true',{creditNote_id:creditNote_id,status:"update"});
                                                alert("Error");
                                            }
                                        }
                                    ,"json");
                                }
                                else{
                                    $('#div_creditNote').html('Loading...');
                                    $('#div_creditNote').load('?m=sales&a=vw_creditNote&c=view_creditNote_detail&suppressHeaders=true',{creditNote_id:creditNote_id,status:"update"});
                                }
                                    
                        },"json");  
                    }
                },"json");
            }
            else if(status=="update_info"){
              $(".loading_active").fadeIn();
                var sales_attention_id = $('#sales_attention_id').val();
//                alert("If there's a change in Credit Note Total, please go to the related payment to update accordingly");
                $.post('?m=sales&a=vw_creditNote&c=_do_update_creditNote&suppressHeaders=true',{creditNote_id:creditNote_id,customer_id:customer_id,address_id:address_id,credit_voucher_no:credit_voucher_no,credit_date_voucher:credit_date_voucher,invoice_id:invoice_id,creditNote_tax:creditNote_tax,creditNote_tax_edit_value:creditNote_tax_edit_value,attention_id:attention_id,sales_attention_id:sales_attention_id,credit_co:credit_co});
//                    function(data){
//                        alert(data);
//                        alert(data.status);
//                        if(data.status=="Success"){
                            creditNote_item_arr = creditNote_item_arr+"&credit_note_id="+creditNote_id;
                            $.post('?m=sales&a=vw_creditNote&c=_do_save_creditNote_item&suppressHeaders=true',creditNote_item_arr,
                            function(data){
                                $('#div_creditNote').html('Loading...');
                                $('#div_creditNote').load('?m=sales&a=vw_creditNote&c=view_creditNote_detail&suppressHeaders=true',{creditNote_id:creditNote_id,status:"update_info"});
                            });
//                        }
//                    }
//                ,"json");
            }
                
        }
    }
    function save_item_creditNote(){
        var description = $('#dis_credit').val();
        var amount = $('#amount_credit').val();
        var order = $('#order_credit').val();
    }
    function load_list_creditNote(){
        $('#div_list_creditNote').html('Loading...');
        $('#div_list_creditNote').load('?m=sales&a=vw_creditNote&c=list_creditNote&suppressHeaders=true');
    }
    function load_back_creditNote(){
        window.open('?m=sales&show_all=1&tab=2','_self');
    }
    function edit_line_creditNoteItem(item){
        var creditNote_order = $('#stt_'+item).html();
        var creditNote_des = $('#creditNote_des_'+item).html();
        var creditNote_amount = $('#hdd_creditNote_amount_'+item).val();
        
        var tr='<td align="center" width="6%"><a href="#" onclick="cancel_edit_line_creditItem('+item+'); return false;">Cancel</a></td>';
        tr+='<td align="center" width="6%"><input type="text" value="'+creditNote_order+'" name="edit_stt_'+item+'" id="edit_stt_'+item+'" class="text" size="4" /></td>';
        tr+='<td ><textarea rows="4" cols="70" id="edit_creditNote_des_'+item+'" name="edit_creditNote_des_'+item+'">'+creditNote_des+'</textarea></td>';
        tr+='<td width="18%" align="center">';
            tr+='<input type="text" value="'+creditNote_amount+'" class="text" id="edit_creditNote_amount_'+item+'" name="dit_creditNote_amount_'+item+'" onchange="load_credit_total();return false;" size="12" />';
            tr+='<input style="margin-left:10px;" type="button" class="ui-button ui-state-default ui-corner-all" name="save_line_credit_item" onclick="save_edit_line_credit_item('+item+'); return false;" id="save_line_credit_item" value="Save" />';
        tr+='</td>';
        $('#row_item_'+item).html(tr);
    }
    function cancel_edit_line_creditItem(item){
        var creditNote_order = $('#edit_stt_'+item).val();
        var creditNote_des = htmlchars($('#edit_creditNote_des_'+item).val());
        var creditNote_amount = $('#edit_creditNote_amount_'+item).val();
        var fomat = parseFloat(creditNote_amount);
        fomat = number_format(fomat,2,'.','');
        var tr='<td align="center"><input type="checkbox" value="'+item+'" name="item_check_list" id="item_check_list"><a class="icon-edit icon-all" onclick="edit_line_creditNoteItem('+item+'); return false;" href="#" title="Inline Edit" style="margin-right:0px"></a></td>';
        tr+='<td id="stt_'+item+'">'+creditNote_order+'</td>';
        tr+='<td id="creditNote_des_'+item+'">'+creditNote_des+'</td>';
        tr+='<td id="creditNote_amount_'+item+'" align="right">$'+fomat;
            tr+='<input type="hidden" id="hdd_creditNote_amount_'+item+'" value="'+creditNote_amount+'" />';
        tr+='</td>';
        $('#row_item_'+item).html(tr);
    }
    function delete_crediNoteItem(crediNote_id){
            var credit_str_id = get_invoiceID_checked('item_check_list');
            //alert(credit_str_id);
            if (credit_str_id != '') {
                if (confirm('<?php echo $AppUI->_('Are you sure delete record(s)?'); ?>')) {
//                    alert(credit_str_id);
                    $.post('?m=sales&a=vw_creditNote&c=_do_delete_crediNote_item&suppressHeaders=true', {crediNote_item_arr:credit_str_id,crediNote_id:crediNote_id},
                        function(data) {
                            if (data.status == 'Success') {
                                $('#div_creditNote').load('?m=sales&a=vw_creditNote&c=view_creditNote_detail&suppressHeaders=true',{creditNote_id:crediNote_id});
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one Credit Note Item to delete'); ?>');
            }
    }
    function get_invoiceID_checked(id_check) {i
            var inputs = document.getElementsByName(id_check);
            var creditNote_item_id = '';
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                    creditNote_item_id += inputs[i].value+",0";
                }
            }
            return creditNote_item_id;
    }
    function load_payment_creditNote(customer_id,creditNote_id,total_amount){
        window.open('?m=sales&show_all=1&tab=3&status=add&customer_id='+customer_id+'&creditNote_id='+creditNote_id+'&total_amount='+total_amount,'_self');
    }
    function load_update_total(){
        //var credit_amount = $('#amount_credit_'+item).val();
        var subtotal = $('#credit_subtotal_hdd').val();
        var subtotal_old = $('#credit_subtotal_hdd_ol').val();
        var credit_tax = $('#credit_tax_value').val();
        var tmp=0;
        for(var i=0;i<credit_item_id_arr.length;i++){
            credit_amount_arr[i] = $('#amount_credit_'+credit_item_id_arr[i]).val();
            tmp = parseFloat(tmp) + parseFloat(credit_amount_arr[i]);
        };
        
        var credit_subtotal = parseFloat(subtotal_old) + parseFloat(tmp);
//        var credit_total = parseFloat(credit_subtotal) + parseFloat(credit_tax);
//        var credit_total_format = credit_total.toFixed(2);
        var credit_subtotal_format = credit_subtotal.toFixed(2);
        var value_tax = 0;
        if($('#creditNote_tax').val()!=""){
            value_tax = $('#creditNote_tax').find(':selected').text();
        }
        var credit_tax = parseFloat(credit_subtotal)*(parseInt(value_tax)/100);
        var credit_tax_format = credit_tax.toFixed(2);
        $('#credit_tax_value').val(credit_tax_format);
        
        $('#credit_subtotal_hdd').val(credit_subtotal);
        $('#credit_subtotal').html('$'+credit_subtotal_format);
//        $('#credit_total').html('$'+credit_total_format)
//        $('#credit_total_hdd').val(credit_total);
        load_credit_total();
    }
    function load_update_total_by_tax(value){
        var credit_subtotal = $('#credit_subtotal_hdd').val();
        var value_tax = 0;
        if(value!=""){
            value_tax = $('#creditNote_tax').find(':selected').text();
        }
        var credit_tax = parseFloat(credit_subtotal)*(parseInt(value_tax)/100);
        var credit_total = parseFloat(credit_subtotal) + parseFloat(credit_tax);
        var credit_subtotal_format = credit_total.toFixed(2);
        var credit_tax_format = credit_tax.toFixed(2);
        $('#credit_tax_value').val(credit_tax_format);
        //$('#credit_tax_hdd').val(credit_tax);
//        $('#credit_total').html('$'+credit_subtotal_format);
//        $('#credit_total_hdd').val(credit_total);\
        load_credit_total();
    }
    function delete_creditNote(){
            var credit_str_id = get_invoiceID_checked('check_list');
            if (credit_str_id != '') {
                if (confirm('<?php echo $AppUI->_('This Credit Note have been applied. The payment for this Credit Note will be deleted if you delete this Credit Note. Do you want to delete this Credit Note?'); ?>')) {
                    $.post('?m=sales&a=vw_creditNote&c=_do_delete_creditNote&suppressHeaders=true', {crediNote_item_arr:credit_str_id},
                        function(data) {
                            if (data.status == 'Success') {
                                load_list_creditNote();
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one Credit Note to delete'); ?>');
            }  
    }
    function _get_print_credit_note(creditNote_id){
        $.ajax({
            type:"POST",
            url: "?m=sales&a=vw_creditNote&c=_form_print&suppressHeaders=true",
            data: "creditNote_id="+creditNote_id,
            success:function(x){
                $('#div-popup').html(x).dialog({
                    resizable: true,
                    modal: true,
                    title: '<?php echo $AppUI->_('Generate'); ?>',
                    width: 400,
                    maxheight: 400,
                    close: function(ev, ui) {
                        $('#div-popup').dialog('destroy');
                    },
                    buttons: {
                        'Cancel': function() {
                            $(this).dialog('close');
                        },
                        'Print': function() {
                            var generate = $('#generate').val();
                            if (generate == 1) {
                                window.open('?m=sales&a=vw_creditNote&c=_do_print_pdf&suppressHeaders=true&creditNote_id='+creditNote_id,'_brank','');
                            } else if (generate == 0) {
                                window.open('?m=sales&a=vw_creditNote&c=_do_print_html&suppressHeaders=true&creditNote_id='+creditNote_id,'_brank','');
                            }
                        }
                    }
                });
                $('#div-popup').dialog('open');
            }
        });
    }
    function popupSendEmail(creditNote_id){
        var id = 'div-popup';
        {
            $('#'+id).html("Loading ...");
            $('#'+id).load('?m=sales&a=vw_creditNote&c=_form_send_email&suppressHeaders=true', {creditNote_id: creditNote_id,}).dialog({
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
        }
    }
    function print_credit(creditNote_id){
        window.open('?m=sales&a=vw_creditNote&c=_do_print_pdf&suppressHeaders=true&creditNote_id='+creditNote_id,'_brank','');
    }
    function setSendButton(creditNote_id){
           {
               var id = 'div-popup';
               if (confirm('<?php echo $AppUI->_('Are you sure to send email(s) to customers?'); ?>')) {
                  var sender = $('#sender').val();
                  var reciver = $('#reciver').val();
                  var subject = $('#subject').val();
                  var content = $('#content').val();
                        $.post('?m=sales&a=vw_creditNote&c=_do_send_email_invoice&suppressHeaders=true', { action: 'send_mail_in', creditNote_id: creditNote_id, sender: sender, reciver: reciver, subject: subject, content: content },
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
    }
//    function load_view_invoice(){
//        var invoice_id = $('#credit_invoice').val();
//        $('#view_invoice').load('?m=sales&a=vw_creditNote&c=_do_load_view_invoice&suppressHeaders=true',{invoice_id:invoice_id});
//     }
function save_edit_line_credit_item(item_id){
    var creditItem_order = $('#edit_stt_'+item_id).val();
    var creditItem_des = $('#edit_creditNote_des_'+item_id).val();
    var creditItem_amount = $('#edit_creditNote_amount_'+item_id).val();
    var creditNote_id = $('#creditNote_id').val();
    var credit_invoice = $('#credit_invoice').val();
    $.post('?m=sales&a=vw_creditNote&c=_do_save_edit_item_ctedit&suppressHeaders=true',{creditNote_id:item_id,creditItem_order:creditItem_order,creditItem_des:creditItem_des,creditItem_amount:creditItem_amount},
        function(data){
            if(data.status=="Success"){
                if(credit_invoice!="")
                {
                    // Thong bao neu Da ap dung Credit cho 1 invoice roi ma muon update Item.
                    alert("If there's a change in Credit Note Total, please go to the related payment to update accordingly.");
                }
                $('#form_item').html('Loading..');
                $('#form_item').load('?m=sales&a=vw_creditNote&c=form_table_item&suppressHeaders=true',{creditNote_id:creditNote_id});
            } 
            if(data.status=="Failure")
                alert('Error');
        },'json');
}
function load_creditNote_status(status){
    $('#div_list_creditNote').load('?m=sales&a=vw_creditNote&c=list_creditNote&suppressHeaders=true',{status:status});
}
function load_credit_total(){
    var credit_tax_value = $('#credit_tax_value').val();
    var credit_subtotal_hdd = $('#credit_subtotal_hdd').val();
    credit_tax_value = quo_remove_commas(credit_tax_value);
    
    var credit_total = parseFloat(credit_subtotal_hdd) + parseFloat(credit_tax_value);
    var credit_total_format = credit_total.toFixed(2);
    
    $('#credit_total').html('$'+credit_total_format);
    $('#credit_total_hdd').val(credit_total);
}
function load_info_attention(attention_id){
    //alert(attention_id);
    $('#info_attention').load('?m=sales&a=vw_creditNote&c=_get_email_by_sales_attention&suppressHeaders=true',{attention_id:attention_id});
}
//function load_address_by_customer($customer_id){
//    
//}
</script>

