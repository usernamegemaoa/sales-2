<script>
        $(document).ready(function() {            
                loadJQueryCalendar('payment_date', 'payment_date_hidden', 'dd/M/yy', 'yy-mm-dd');
                var tr ='<tfoot id="add-payment-inline-foot">';
                    tr+='<input type="hidden" id ="count_row_payment_invoive" value="1">';
                    tr+='</tfoot>';
                    $('#payment_ivoice_table').append(tr);
                
                    var customer_id = $('#payment_customer_select').val();
                    $('#payment_customer_select').chosen();

               if(customer_id!=""){
                    load_custoner__(customer_id);
//                    alert('123');
//                    add_line_payment_invoice();
               }
	});
         
    inv_payment_arr = new Array();
    row_inv_id = new Array();
//    function add_line_payment_invoice(){
//        var customer_id_chose = $('#payment_customer_select').val();
//        if(customer_id_chose == ""){
//            alert('Please choose Customer');
//            return;
//        }
//        var credit_amount =0;
//        var amount_due = 0;
//        var count_row = $('#count_row_amount_payment').val();
//        var total_row = $('#total_credit_amount').val();
//        credit_amount = $('#hdd_credit_amount').val();
//        var total_amount = "";
//        var payment_invoice_rev = $('#invoice_id').val();
//        var element_id = payment_invoice_rev.split(",");
//        var invoice_id = element_id[0];
//        var invoice_rev_id = element_id[2];
//        var invoice_no = element_id[5];
//        var customer_id = element_id[1];
//        var pay_creditNote_id = $('#pay_creditNote_id').val();
//        amount_due = element_id[6];
//        inv_payment_arr.push(count_row);
//        //total_amount = parseFloat(amount_due) - parseFloat(credit_amount);
//        var tr='';
//        tr +='<tr id="row_amount_'+count_row+'">';
//            tr +='<td align="center"><img border="0" style="cursor: pointer" onclick="remove_invoice_inline('+count_row+','+customer_id+','+invoice_id+')" src="images/icons/stock_delete-16.png"></td>';
//            tr +='<td align="center">';
//            tr +=count_row;
//            tr +='<input type="hidden" id="invoice_revision_id_'+count_row+'" value="'+invoice_rev_id+'" />';
//            tr +='<input type="hidden" id="invoice_id_'+count_row+'" value="'+invoice_id+'" />';
//            if(pay_creditNote_id!="")
//                tr += '<input type="hidden" id="creditNote_id_'+count_row+'" value="'+pay_creditNote_id+'" />';
//            tr +='</td>';
//            tr +='<td>'+invoice_no+'</td>';
//            tr +='<td align="right" width="25%">$'+number_format(amount_due,2,'.',',')+'<input type="hidden" id="amount_invoice_'+count_row+'" value="'+amount_due+'" /></td>';
//            tr +='<td align="center" width="35%">';
//                tr +='<input type="text" class="text" id="payment_amount_'+count_row+'" value="" name="payment_amount" />';
//            tr +='</td>';
//        tr +='</tr>';
//        if(total_row==0){
//            $('#add-payment-inline').html('');
//        }
//        $('#add-payment-inline').append(tr);
//        $('#count_row_amount_payment').val(parseInt(count_row)+1);
//        $('#total_credit_amount').val(parseInt(total_row)+1);
//        row_inv_id.push(invoice_id);
//        $('#invoice_id').load('?m=sales&a=vw_payment_new&c=get_invoiceBycustomer_select&suppressHeaders=true',{customer_id:customer_id_chose,row_inv_id:row_inv_id});
//    }
    function save_payment_add(){
        
        var payment_receipt_no = $('#payment_receipt_no').val();
        var payment_customer_select = $('#payment_customer_select').val();
        var payment_method = $('#payment_method').val();
        var payment_date = $('#payment_date_hidden').val();
        var payment_notes = $('#payment_notes').val();
        var bank_account_id = $('#bank_account_id').val();
        var payment_cheque_nos = $('#payment_cheque_nos').val();
        var payment_description = $('#payment_description').val();
        
        var payment_invoice = new Array();
        var invoice_id = new Array();
        var invoice_rev_id = new Array();
        var payment_amount = $('#payment_amount').val();
        var total_paymen = $('#hdd_total_paymen').val();
        var payment_detail_note = new Array();
        if(trim(payment_receipt_no)==""){
            alert ('Sorry. This Receipt No is empty');
            return false;
        }
        if (payment_customer_select == ""){
            alert('Please choose Customer');
            return false;        }

        for(var i=0;i<inv_payment_arr.length;i++){
            var j = inv_payment_arr[i];
            //alert(j);
            payment_invoice[i] = $('#payment_invoice_'+j).val();
            invoice_rev_id[i] = $('#hdd_invoice_rev_'+j).val();
            payment_detail_note[i] = $('#payment_detail_notes_'+j).val();
            invoice_id[i] = j;
        }
        if(inv_payment_arr.length==0){
            alert('Please select invoice to payment.');
            return false;
        }
        if(parseFloat(payment_amount)!=parseFloat(total_paymen)){
            alert("Your total applied payment is not equal to payment amount. Please make sure you apply all the payment amount");
            return false;
        }
        $(".loading_active").fadeIn();
        $.post('?m=sales&a=vw_payment_new&c=_do_payment_new&suppressHeaders=true',{payment_date: payment_date,payment_method:payment_method,payment_notes:payment_notes,payment_receipt_no:payment_receipt_no,bank_account_id:bank_account_id,payment_cheque_nos:payment_cheque_nos,payment_description:payment_description},
                function(data){
                    if(data.status == 'Success'){
                        var payment_id = data.payment_id;
                        $.post('?m=sales&a=vw_payment_new&c=_do_add_paymentDetail_new&suppressHeaders=true',{payment_id:payment_id,payment_amount: payment_invoice,payment_invoice_rev_id:invoice_rev_id,invoice_id:invoice_id,payment_detail_note:payment_detail_note},
                            function(data){
                                if(data.status == 'Success'){
                                    alert("Successful Payment");
                                    $('#new_payment_new').html('');
                                }else if(data.status == 'Failure'){
                                    alert(data.message);
                                }
                            },"json");
                            
                    }else if(data.status == 'Failure'){
                        alert(data.message);}
            },"json");
    }
    
//    function save_payment_add(){
//        var tmp_invoi = true, tmp_amount=0;
//        var count_payment_invoice = $('#count_row_payment_invoive').val();
//        var payment_amount = new Array();
//        var invoice_amount = new Array();
//        var payment_invoice_rev_id = new Array();
//        var invoice_id = new Array();
//        var customer_id = new Array();
//        var total_invoice = new Array();
//        var ivoice_rev_id = new Array();
//        var total_amount = new Array();
//        var total_credit_amount = $('#total_credit_amount').val();
//        var customer_id_select = $('#payment_customer_select').val();
//        var total_row = $('#total_credit_amount').val();
//        var pay_creditNote_id = $('#pay_creditNote_id').val();
////        var credit_total = $('#hdd_credit_amount').val();
////        alert(credit_total);
//        for(var i=0;i<inv_payment_arr.length;i++){
//            var j= inv_payment_arr[i];
//            payment_amount[i] = $('#payment_amount_'+j).val();
//            invoice_amount[i] = $('#amount_invoice_'+j).val();
//            ivoice_rev_id[i] = $('#invoice_revision_id_'+j).val();
//            total_amount[i] = parseFloat(payment_amount[i]);
//            var tmp_amount =0;
//            if(trim(payment_amount[i])==''){
//                tmp_amount=1;
//            }else if(isNaN(payment_amount[i])==true){
//                tmp_amount=2;
//            }else if(parseFloat(payment_amount[i])>parseFloat(invoice_amount[i])){
//                tmp_amount=3;
//            }else if(parseFloat(payment_amount[i])<=0){
//                tmp_amount=4;
//            }
//        }
//        var payment_receipt_no = $('#payment_receipt_no').val();
//        var payment_date = $('#payment_date').val();
//        var payment_method = $('#payment_method').val();
//        var payment_notes = $('#payment_notes').val();
//        if(trim(payment_receipt_no)==""){
//                alert ('Sorry. This Receipt No is empty');
//                return;
//        }
//        if (customer_id_select == ""){
//            alert('Please choose Customer');
//            return;
//        }else if(tmp_amount == 1){
//            alert('Please enter payment amount');
//            return false
//        }else if(tmp_amount == 2){
//            alert('Payment amount invalid');
//            return false;
//        }else if(tmp_amount == 3){
//            alert('The Payment Amount cannot exceed the Invoice Balance');
//            return false;
//        }else if(tmp_amount == 4){
//            alert('Please enter Quatity must  greater than 0');
//            return;
//        }else if(total_row<=0){
//            alert('Please enter Add invoice');
//            return;
//        }else{
//        $.post('?m=sales&a=vw_payment_new&c=_do_payment_new&suppressHeaders=true',{payment_date: payment_date,payment_method:payment_method,payment_notes:payment_notes,payment_receipt_no:payment_receipt_no},
//                    function(data){
//                    if(data.status == 'Success'){
//                        var payment_id = data.payment_id;
//                        $.post('?m=sales&a=vw_payment_new&c=_do_add_paymentDetail_new&suppressHeaders=true',{payment_id:payment_id,payment_amount: total_amount, payment_invoice_rev_id:ivoice_rev_id,count_payment_invoice:count_payment_invoice},
//                            function(data){
//                                if(data.status == 'Success'){
////                                    var payment_invoice_id = new Array();
////                                    if(pay_creditNote_id!="")
////                                        for(var i=0;i<inv_payment_arr.length;i++){
////                                            var j= inv_payment_arr[i];
////                                            var invoice_id = $('#invoice_id_'+j).val();
////                                            var credit_id = $('#creditNote_id_'+j).val();
////                                            if(credit_id!=undefined)
////                                                $.post('?m=sales&a=vw_creditNote&c=_do_update_status_creditNote&suppressHeaders=true',{creditNote_id:credit_id,credit_note_status:2,invoice_id:invoice_id});
////                                        }
//                                     $('#new_payment_new').html('');
//                                    
//                                }else if(data.status == 'Failure'){
//                                    alert(data.message);
//                                }
//                            },"json");
//                            
//                    }else if(data.status == 'Failure'){
//                        alert(data.message);}
//                    },"json");
//
//                    return true
//        }
//        
//    }
//    function add_new_payment_new(){
//        $('#new_payment_new').html('Loading...');
//        $('#new_payment_new').load('?m=sales&a=vw_payment_new&c=vw_add_payment_new&suppressHeaders=true');
//        
//    }
    function remove_invoice_inline(key,customer_id,invoice_id) {
        var count_row = $('#count_row_amount_payment').val();
        var total_row = $('#total_credit_amount').val();
        var a = key.toString();
        var item_id = inv_payment_arr.indexOf(a);
        inv_payment_arr.splice(item_id,1);
        invoice_id = invoice_id.toString();
        var invoice_id_key =  row_inv_id.indexOf(invoice_id);
        row_inv_id.splice(invoice_id_key,1);
        $('#row_amount_'+ key).remove();
        if(count_row==2){
            $('#add-payment-inline').html('<tr><td colspan="5">No invoice</td></tr>');
        }
        $('#total_credit_amount').val(parseInt(total_row)-1);
        $('#invoice_id').load('?m=sales&a=vw_payment_new&c=get_invoiceBycustomer_select&suppressHeaders=true',{customer_id:customer_id,row_inv_id:row_inv_id});
    }
    
    function load_custoner__(customer_id){
            if(customer_id!=undefined){
                $('#payment_invoice_detail').load('?m=sales&a=vw_payment_new&c=get_invoiceBycustomer_select&suppressHeaders=true',{customer_id:customer_id});
                $('#submit_add_invoice').load('?m=sales&a=vw_payment_new&c=get_add_line_invoice&suppressHeaders=true',{customer_id:customer_id});
                $('#pay_creditNote_id').load('?m=sales&a=vw_payment_new&c=get_credit_by_customer&suppressHeaders=true',{customer_id:customer_id});
                $('#add-payment-inline').html('<tr><td colspan="5">No invoice</td></tr>');
                $('#count_row_amount_payment').val(1);  
                inv_payment_arr = new Array();
                row_inv_id = new Array();
                var payment_amount_total = $('#payment_amount').val();
                $('#hdd_invoice_payment_amount').val(payment_amount_total);
            }
    }
    
    function trim(str){
        while(str.substring(0,1)==" "){
            str=str.substring(1, str.length);
            }
                while(str.substring(str.length-1,str.length)==" "){
                    str=str.substring(0, str.length-1);
            }
            return str;
    }
    function load_total_amount_credit(creditNote_id){
        var credit_note_no = $('#pay_creditNote_id').find(':selected').text();
        $('#credit_amount').load('?m=sales&a=vw_payment_new&c=get_total_credit_customer&suppressHeaders=true',{creditNote_id:creditNote_id});
        $('#payment_notes').val(credit_note_no);
    }
    function applier_invoice(row_id){
        var amount_total = $('#hdd_credit_amount').val();
        alert(amount_total);
        $('#payment_amount_credi'+row_id).val(amount_total);
    }
    function apply_credit(){
       alert('123'); 
    }
    function load_payment_total(invoice_id){
        var payment_amount_total = $('#hdd_invoice_payment_amount').val();
        var payment_amount_due = $('#hdd_invoice_payment_amount_'+invoice_id).val();
        var customer_id = $('#payment_customer_select').val();
        var tick = 1;
            
        var total = parseFloat(payment_amount_total) - parseFloat(payment_amount_due);
        total = total.toFixed(2);
        if($('#list_checkbox_'+invoice_id).is(":checked")){
            if(payment_amount_total==""){
                alert('Please enter for payment!');
                load_custoner__(customer_id);
                return false;
            }
            if(payment_amount_total<0 || isNaN(payment_amount_total)){
                alert('Please enter payment amount must  greater than 0');
                load_custoner__(customer_id);
                return false;
            }
            inv_payment_arr.push(invoice_id);
            if(total>0){
                $('#payment_invoice_'+invoice_id).val(payment_amount_due);
                $('#hdd_payment_invoice_'+invoice_id).val(payment_amount_due);
                $('#hdd_invoice_payment_amount').val(total);
            }
            else{
                $('#payment_invoice_'+invoice_id).val(payment_amount_total);
                $('#hdd_payment_invoice_'+invoice_id).val(payment_amount_total);
                $('#hdd_invoice_payment_amount').val('0');
            }
            document.getElementById('payment_invoice_'+invoice_id).readOnly=false;
            document.getElementById('payment_detail_notes_'+invoice_id).readOnly=false;
            load_total_payment(invoice_id,tick);
        }else{
            tick = -1
            // Loai bo invoice id khoi mang
            var a = invoice_id.toString();
            var item_id = inv_payment_arr.indexOf(a);
            inv_payment_arr.splice(item_id,1);
            payment_amount_due = $('#payment_invoice_'+invoice_id).val();
            total = parseFloat(payment_amount_total) + parseFloat(payment_amount_due);
            $('#payment_invoice_'+invoice_id).val('0.00');
            $('#hdd_invoice_payment_amount').val(total);
            document.getElementById('payment_invoice_'+invoice_id).readOnly=true;
            load_total_payment(invoice_id,tick);
        }
    }
    function load_payment_amount_hd(){
        var customer_id = $('#payment_customer_select').val();
        var payment_amount_total = $('#payment_amount').val();
        $('#hdd_invoice_payment_amount').val(payment_amount_total);
        load_custoner__(customer_id);
    }
    function load_total_payment(item,tick){
        var payment = 0;
        var total_payment = 0;
        var format_total
        var i;
        var j;
        if(tick==1 || tick==0){
            for( i=0;i<inv_payment_arr.length;i++){
                j = inv_payment_arr[i];
                payment = $('#payment_invoice_'+j).val();
                total_payment += parseFloat(payment);
            }
            total_payment = total_payment.toFixed(2);
            format_total = number_format(total_payment,2,".",",")
            $('#total_paymen').html('$'+format_total);
            $('#hdd_total_paymen').val(total_payment);
        }
        else if(tick==-1){
            payment =0;
            total_payment = 0;
            for( i=0;i<inv_payment_arr.length;i++){
                j = inv_payment_arr[i];
                payment = $('#payment_invoice_'+j).val();
                total_payment += parseFloat(payment);
            }
            total_payment = total_payment.toFixed(2);
            format_total = number_format(total_payment,2,".",",")
            $('#total_paymen').html('$'+format_total);
            $('#hdd_total_paymen').val(total_payment);

        }
        check_payment_larger(item);
        //alert(inv_payment_arr);
    }

    function check_payment_larger(item){
        var pay_inv_amount = $('#hdd_invoice_payment_amount_'+item).val();
        var hdd_payment_invoice = $('#hdd_payment_invoice_'+item).val();
        var payment_invoice = $('#payment_invoice_'+item).val();
        var payment_amount = $('#payment_amount').val();
        var hdd_total_payment = $('#hdd_total_paymen').val();
        if(parseFloat(pay_inv_amount) < parseFloat(payment_invoice)){
            alert('The Payment Amount cannot exceed the Invoice Balance');
            $('#payment_invoice_'+item).val(hdd_payment_invoice);
            load_total_payment(item,0);
        }else{
            var hdd_payment_amount = parseFloat(payment_amount) - parseFloat(hdd_total_payment);
            hdd_payment_amount = hdd_payment_amount.toFixed(2);
            $('#hdd_invoice_payment_amount').val(hdd_payment_amount);
        }
    }
 </script>
