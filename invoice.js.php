<script type="text/javascript">
        var count_inv_click_edit1=1;
        var count_inv_click_edit=1;
        var invoice_item_arr = new Array();
        var inv_item_add_arr = new Array();
        var tmp_inv_arr = new Array();
        var change_value = 0;
       
	$(document).ready(function() {
                
                loadJQueryCalendar('invoice_date_display', 'invoice_date', 'dd/mm/yy', 'yy-mm-dd');
                
                var search_status_id = $('#invoice_status_id').val();
                var search_invoice_no = $('#search-invoice-no').val();
                var search_contract_no = $('#search-invoice-by-contract').val();

                $('#invoice_table').dataTable({
                    'sprocessing':true,
                    'bServerSide':true,
                    "bDestroy": true,
                    "border": [[ 0, "desc" ]],
                    'sAjaxSource':'?m=sales&a=server_processing_invoice&invoice_no='+search_invoice_no+'&status_id='+search_status_id+'&contract_no='+search_contract_no+'&suppressHeaders=true',
                    'aoColumns':[
                        {"align": "left","asSorting": [ "desc" ]},
                        {"align": "left"},
                        {"align": "left"},
                        {"align": "left"},
                        {"align": "left"},
                        {"align": "left"},
                        {"align": "right"},
                        {"sClass": "text_right"},
                        {"sClass": "invoice_status1"},
                    ],
                    "fnDrawCallback": function( oSettings ) {
                        $('.invoice_status').editable('?m=sales&a=vw_invoice&c=_do_update_invoice_field&suppressHeaders=true&col_name=invoice_status', {
                                indicator    : 'Loading...',
                                width: 220,
                                loadurl : '?m=sales&a=vw_invoice&c=get_invoice_status&suppressHeaders=true',
                                type: 'select',
                                submit: 'Save',
                                cancel: 'Cancel',
                                height: 20
                            });
                    }
                });
                
                $('#invoice_sortable tr').css({'cursor': 'pointer'});

                $('#invoice_sortable tr').click(function () {    
                    console.log('click');
                    
                });
                var tr = '<tfoot id="add-invoi-inline-foot">';
                tr += '<input type="hidden" id="count_row_item" value="1">';
                tr += '</tfoot>';
                $('#detail_invoice_table').append(tr);
                
                var oTable = $('#detail_invoice_table').dataTable();
                
                
                $('#div_invoice_info input').css({'float':'right','width':'200px'});
                $('#div_invoice_info select').css({'float':'right'});
                $('#div_invoice_info p').css({'overflow':'hidden','padding-bottom':'5px'});
                $('#div_2_right p').css({'overflow':'hidden','padding-bottom':'5px'});
                $('#div_2_right input').css({'float':'right','margin-right':'0px','width':'200px'});
                $('#div_2_client').css({'overflow':'visible'});
                $('#div_2_client p').css({'padding-bottom':'5px'});
                $('#div_2_joblocation').css({'overflow':'visible'});
             

                $('#inv_customer_id').select2();
                $('#inv_address_id').select2();
                
                $('#inv_job_location_id').select2();
                $('#int_invoice_CO').select2();
                $('#sel_customer .select2-container').css({'top':'8px'});
                $('.select2-container').css({'width':'393px'});
                $('#sel_address .select2-container').css({'width':'500px'});
                //$('#sel_job_location .select2-container').css({'width':'500px'});
                $('#div-list-invoice').css({overflow:'visible'});
                
                
                
	});
        load_update_term_invoice();
        function list_invoice(customer_id, status_id, status_rev,invoice_id, invoice_revision_id,invoice_no,contract_no) {
            var inv = $('#inv_id').val();
            var filters="";
            if(inv==undefined)
                $('#button_new').css({display:'none'});
            else
            {
                $('#button_new').css({display:'block'});
                filters = $('#filter_search').val();
            }
            
            if(status_rev == undefined || status_rev!='update'){
                if(invoice_no==undefined)
                    invoice_no = $('#search-invoice-no').val();
                if(contract_no==undefined)
                    contract_no = $('#search-invoice-by-contract').val();
                $('#div-list-invoice').html('Loading...');
                $('#div-list-invoice').load('?m=sales&a=vw_invoice&c=list_invoice_html&suppressHeaders=true', { customer_id: customer_id, status_id: status_id,invoice_no:invoice_no,contract_no:contract_no },function(){
                    $('#invoice_table_filter label input[type=text]').val(filters).trigger($.Event("keyup", { keyCode: 13 }));
                } );
            }
            else{
                $('#div_invoice').html('<?php echo $AppUI->_('Loading...');?>');
                $('#div_invoice').load('?m=sales&a=vw_invoice&c=view_invoice_detail&suppressHeaders=true', { invoice_id: invoice_id, invoice_revision_id: invoice_revision_id, status_rev: "update" });
            }
        }
        
//        function load_quotation(quotation_id, quotation_revision_id, status_rev) {
//            $('#div_invoice').html('<?php echo $AppUI->_('Loading...');?>');
//            $('#div_invoice').load('?m=sales&a=vw_quotation&c=view_quotation_detail&suppressHeaders=true', { quotation_id: quotation_id, quotation_revision_id: quotation_revision_id, status_rev: status_rev });
//        }

        function load_invoice(invoice_id, invoice_revision_id, status_rev) {
            var inv = $('#inv_id').val();
            var filter = $('#invoice_table_filter label input[type=text]').val();
            if(inv==undefined)
                $('#button_new').css({display:'none'});
            else
                $('#button_new').css({display:'block'});
            var status_id = $('#invoice_status_id').val();
            $('#div-list-invoice').html('Loading...');
            $('#div-list-invoice').load('?m=sales&a=vw_invoice&c=view_invoice_detail&suppressHeaders=true', { invoice_id: invoice_id, invoice_revision_id: invoice_revision_id, status_rev: status_rev, status_id:status_id,filter:filter });
        }

        function change_revision(invoice_id, invoice_revision_id, status_rev) {

                    var id = 'div-popup';

                    $('#'+id).html("Loading ...");
                    $('#'+id).load('?m=sales&a=vw_invoice&c=change_revision_html&suppressHeaders=true', { invoice_id: invoice_id, invoice_revision_id: invoice_revision_id, status_rev: status_rev }).dialog({
                                resizable: false,
                                modal: true,
                                title: '<?php echo $AppUI->_('List Revision'); ?>',
                                width: 450,
                                maxheight: 400,
                                close: function(ev,ui){
                                    $('#'+id).dialog('destroy');
                                }
                    });
                    $('#'+id).dialog('open');
                    
                    
        }

//        function save_all_invoice(invoice_id, invoice_revision_id, status_rev) {
//
//            //_disable_btn('btn-save-all'); // disable button
//
//            var mang_giatri = $('#frm_all_invoice').serialize();
//                mang_giatri = mang_giatri+'&invoice_id='+ invoice_id +'&invoice_revision_id='+ invoice_revision_id +'&status_rev='+ status_rev;
//            
//            $.post('?m=sales&a=vw_invoice&c=_do_add_invoice&suppressHeaders=true', mang_giatri,
//                function(data) {
//                    if (data.status == 'Success') {
//                        load_invoice(data.invoice_id, data.invoice_revision_id, status_rev);
//                    } else if (data.status == "Failure") {
//                        alert(data.message);
//                    }
//            }, "json");
//            
//        }
        function save_all_invoice(invoice_id, invoice_revision_id, status_rev) {
            //_disable_btn('btn-save-all');
            var invoice_no = 0;
            var invoice_rev = 0;
            var revision_lastest = $('#revision_lastest').val();
            if($('#invoice_no').val() && $('#invoice_revision').val()){
                invoice_no = $('#invoice_no').val();
                invoice_rev = $('#invoice_revision').val();
            }else{
                invoice_no = $('#hdd_invoice_no').val();
                invoice_rev = invoice_no+'-001';
            }

            var customer_id = $('#inv_customer_id').val();
            var invoice_sale_person = $('#invoice_sale_person').val();
            var invoice_sale_person_email = $('#invoice_sale_person_email').val();
            var invoice_sale_person_phone = $('#invoice_sale_person_phone').val();
            
            var count_total_item = $('#count_total_item').val();
            var count_row_item = $('#count_row_item').val();
            var invoice_item_price = new Array();
            var invoice_item_quantity = new Array();
            var invoice_item = new Array();
            var invoice_item_discount = new Array();
            var invoice_item_stt = new Array();
            var check = true;
            var inv_service_id = $('#invoice_serviceoder').val();
            
//            if (trim(invoice_no)==""){ 
//                alert ('Sorry. This Quotation No is empty');
//                return;
//            }
            if(customer_id == "" || customer_id == 0){
                alert('Please provide client name');
                check = false;
                return;
            }
//            if(invoice_sale_person == "" || invoice_sale_person_email == "" || invoice_sale_person_phone == ""){
//                alert ('Please provide Sales contact');
//                check = false;
//                return
//            }
            if(trim(invoice_sale_person_email)!="" && test_email(invoice_sale_person_email) == false){
                alert ('Invalid Email');
                check = false;
                return;
            }
            if(isNaN(invoice_sale_person_phone)==true){
                alert ('Phone must is number');
                check = false;
                return;
            }
            if(count_total_item == "" && count_row_item<=1){
                alert("Please enter at  least a Item's detail");
                check = false;
                return;
            }
            if(no_special(invoice_no) == false){
                alert ('Sorry. This Invoice No enter special characters.');
                check = false;
                return;
            }
            var check_item = 0;
            var invoice_arrr_item="";
            for(i=1;i<=inv_item_add_arr.length;i++){
                var j = inv_item_add_arr[i-1];
                invoice_item_price[i] = $('#invoice_item_price_'+j).val();
                invoice_item_quantity[i] = $('#invoice_item_quantity_'+j).val();
                invoice_item[i] = $('#invoice_item_'+j).val();
                invoice_arrr_item += '&invoice_arrr_item[]='+enter_br(invoice_item[i]);
                invoice_item_discount[i] = $('#invoice_item_discount_'+j).val();
                invoice_item_stt[i] = $('#invoice_order_'+j).val();
                if(trim(invoice_item[i]) =="")
                    check_item = 1;
                else if(isNaN(invoice_item_price[i]) == true){
                    check_item = 2; 
                }
                else if(invoice_item_quantity[i] <0 || isNaN(invoice_item_quantity[i]) == true){
                    check_item = 3;
                }
                else if(isNaN(invoice_item_discount[i]) == true || invoice_item_discount[i] <0 || invoice_item_discount[i] >100)
                    check_item = 4;
                else if(is_int(invoice_item_stt[i])==false || is_int(invoice_item_quantity[i])==false){
                    check_item = 5;
                }
            }
            //alert(invoice_arrr_item);
            if(check_item == 1){
                alert("Please enter item's name");
                check = false;
                return;
            }
            if(check_item == 2){
                alert('Please enter Price must is number and greater than 0');
                check = false;
                return;
            }
            if(check_item == 3){
                alert('Please enter Quatity must is interger and  greater than 0');
                check = false;
                return;
            }
            if(check_item == 4){
                alert('Please enter Discount must is number and between 0 and 100');
                check = false;
                return;
            }
            if(check_item == 5){
                alert('Please enter # must is integer');
                check = false;
                return;
            }
            
            if(check == true){
                  var mang_giatri1 = $('#frm_all_invoice').serialize();
                  var mang_giatri = $('#frm_all_invoice').serialize();
//                  alert(mang_giatri);
                  mang_giatri = mang_giatri+'&invoice_id='+ invoice_id +'&invoice_revision_id='+ invoice_revision_id +'&status_rev='+ status_rev+invoice_arrr_item;
//                  alert(mang_giatri);
                  if(status_rev == 'update_no_rev') {
                      
                      $(".loading_active").fadeIn();
                      mang_giatri = mang_giatri+'&invoice_no='+invoice_no+'&invoice_revision='+invoice_rev;
                  $.post('?m=sales&a=vw_invoice&c=_do_add_invoice&suppressHeaders=true', mang_giatri,
                        function(data) {
                            if (data.status == 'Success') {
                                if(data.exist_item==1)
                                    alert("Adding item failed. Item with the exact same description and amount already exists");
                                $("#loading").dialog('close');
                                load_invoice(data.invoice_id, data.invoice_revision_id, status_rev);
                                sales_done_serviceoder(inv_service_id,invoice_id,"invoice");
                                $.post('?m=sales&a=vw_invoice&c=_do_update_status_invoice1&suppressHeaders=true?invoice_id='+data.invoice_id,{invoice_save_id:data.invoice_id});
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                        }, "json");
                }
                else if (status_rev == 'update'){    //Neu la update invoice_revision thi hien thong bao (Anhnn)
                    
//                    
                    if (confirm('You are saving an OLDER revision '+ invoice_rev +' . But your latest revision is '+ revision_lastest +'. Are you sure?')) 
                    
                    {
                        $(".loading_active").fadeIn();
                        $.post('?m=sales&a=vw_invoice&c=_do_add_invoice&suppressHeaders=true', mang_giatri,
                        function(data) {
                            if(data.exist_item==1)
                                alert("Adding item failed. Item with the exact same description and amount already exists");
                            if (data.status == 'Success') {
                                $("#loading").dialog('close');
                               load_invoice(data.invoice_id, data.invoice_revision_id, status_rev);
                               $.post('?m=sales&a=vw_invoice&c=_do_update_status_invoice1&suppressHeaders=true?invoice_id='+data.invoice_id,{invoice_save_id:data.invoice_id});
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                        }, "json");
                    }
                }
                else if(status_rev == 'add')
                {
                    
                    $(".loading_active").fadeIn();
                    $.post('?m=sales&a=vw_invoice&c=_do_add_invoice&suppressHeaders=true', mang_giatri,
                    function(data) {
                        if(data.exist_item==1)
                              alert("Adding item failed. Item with the exact same description and amount already exists");
                        if (data.status == 'Success') {
                            $("#loading").dialog('close');
                            load_invoice(data.invoice_id, data.invoice_revision_id, status_rev);
                        } else if (data.status == "Failure") {
                            alert(data.message);
                            load_invoice_no_exist();
                        }
                    }, "json");
                }
                else if(status_rev == 'as_draft')
                {
                    
                    $(".loading_active").fadeIn();
                    $.post('?m=sales&a=vw_invoice&c=_do_add_invoice&suppressHeaders=true', mang_giatri,
                    function(data) {
                        if(data.exist_item==1)
                              alert("Adding item failed. Item with the exact same description and amount already exists");
                        if (data.status == 'Success') {
                             $("#loading").dialog('close');
                            load_invoice(data.invoice_id, data.invoice_revision_id, status_rev);
                        } else if (data.status == "Failure") {
                            alert(data.message);
                        }
                    }, "json");  
                }
            }
            var contacts_title_id = $('#contacts_title_id').val();
            var inv_attention_id = $('#inv_attention_id').val();
            $.post('?m=contacts&a=ajax_edit_contact_title&suppressHeaders=true',mang_giatri1);
        }
        
        
        function delete_invoice(invoice_id, invoice_revision_id) {
            var invoice_str_id = get_invoiceID_checked('check_list', 'invoice_id');
            var invoice_status_arr = new Array();
            // Kiem tra Invoice co status la Draft ko.
            var inputs = document.getElementsByName("check_list");
            var id = '';
            var check =true;
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                    id =inputs[i].value;
                    invoice_status_arr[i] = $('#invoice_status_'+id).val();
                    if(invoice_status_arr[i] != 5){
                        check = false;
                    }
                }
            }
            if(check == false){
                alert("You are only allowed to delete a Draft invoice.");
                return;
            }
            
            
            if (invoice_str_id != '') {
                if (confirm('<?php echo $AppUI->_('Are you sure delete record(s)?'); ?>')) {
                    $.post('?m=sales&a=vw_invoice&c=_do_remove_invoice'+ invoice_str_id +'&suppressHeaders=true', {  },
                        function(data) {
                            if (data.status == 'Success') {
                                search_invoice();
                                $.post('?m=sales&a=vw_invoice&c=_do_inv_update_done_sevice_order'+ invoice_str_id +'&suppressHeaders=true', {  },
                                    function(data){
                                        
                                    },"json");
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one Invoice to delete'); ?>');
            }
        }
        
        function delete_invoice_item(invoice_id, invoice_revision_id, status_rev) {
            var invoice_str_id = get_invoiceID_checked('item_check_list', 'invoice_item_id');
            if (invoice_str_id != '') {
                if (confirm('<?php echo $AppUI->_('Are you sure delete record(s)?'); ?>')) {
                    $.post('?m=sales&a=vw_invoice&c=_do_remove_invoice_item'+ invoice_str_id +'&suppressHeaders=true', { invoice_id: invoice_id, invoice_revision_id: invoice_revision_id, status_rev: status_rev },
                        function(data) {
                            if (data.status == 'Success') {
                                load_invoice(data.invoice_id, data.invoice_revision_id, status_rev);
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one Invoice to delete'); ?>');
            }
        }

        function delete_invoice_revision(invoice_id, invoice_revision_id, countRev) {
            // Kiem tra invoice da co Payment chua
            var check_payment = $('#int_revision_check_payment').val();
            if(check_payment==1){
                alert("You are not allowed to delete this invoice revision, because it has a payment.");
                return false;
            }
            if(countRev<=1){
                alert("you can not Delete all the Invoice Revesion in 1 Invoice");
                return false;
            }
            if (confirm('<?php echo $AppUI->_('Are you sure delete record(s)?'); ?>')) {
                if (invoice_revision_id != '' && invoice_revision_id != null) {     
                    $.post('?m=sales&a=vw_invoice&c=_do_remove_invoice_revision&suppressHeaders=true', { invoice_id: invoice_id, invoice_revision_id: invoice_revision_id },
                        function(data) {
                            if (data.status == 'Success') {
                                load_invoice(data.invoice_id, data.invoice_revision_id, 'update')
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
            }
        }

        function load_invocie_rev(invoice_id) {
            var id_content = $('#div-invoice-rev-'+invoice_id).html();
            if (id_content == '') {
                $('#div-invoice-rev-'+invoice_id).hide()
                $('#div-invoice-rev-'+invoice_id).html('loading...');

                $('#div-invoice-rev-'+invoice_id).load('?m=sales&a=vw_invoice&c=change_revision_html&suppressHeaders=true', { invoice_id: invoice_id })

            }
        }

        function invoice_rev_more(invoice_id) {
            $('#div-invoice-rev-'+invoice_id).toggle('slow');
            //if ($('#div-invoice-rev-'+invoice_id).hide() == true)
              //  $('#invoice-rev-more-'+invoice_id).html('(+)');
            //if ($('#div-invoice-rev-'+invoice_id).show() == true)
              //  $('#invoice-rev-more-'+invoice_id).html('(-)');
        }

        function email_invoice(invoice_id, invoice_revision_id) {
            if (invoice_id == 0 && invoice_revision_id == 0) { // viec gui mail thuc hien o ngoai danh sach cac invoice
                var invoice_str_id = get_invoiceID_checked('check_list', 'invoice_id');
                if (invoice_str_id != '') {
                    if (confirm('<?php echo $AppUI->_('Are you sure send email(s) to customers?'); ?>')) {
                        $.post('?m=sales&a=vw_invoice&c=_do_send_email_invoice'+ invoice_str_id +'&suppressHeaders=true', { action: 'send_mail_beside' },
                            function(data) {
                                alert(data.message);
                        }, "json");
                    }
                } else {
                    alert('<?php echo $AppUI->_('Please select at least one Invoice to send email'); ?>');
                }
            } else { // gui mail thuc hien khi view 1 invoice revision
                    if (confirm('<?php echo $AppUI->_('Are you sure send email(s) to customers?'); ?>')) {
                        $.post('?m=sales&a=vw_invoice&c=_do_send_email_invoice&suppressHeaders=true', { action: 'send_mail_in', invoice_id: invoice_id, invoice_revision_id: invoice_revision_id },
                            function(data) {
                                alert(data.message);
                        }, "json");
                    }
            }
        }


     function popupSendEmail(invoice_id, invoice_revision_id){
        var id = 'div-popup';
        var attention_id = $('#attention_id').val();

        if (invoice_id == 0 && invoice_revision_id == 0){
            var invoice_str_id = get_invoiceID_checked('check_list_quo', 'invoice_id');
            $('#'+id).html("Loading ...");
            $('#'+id).load('?m=sales&a=vw_invoice&c=_form_send_mail'+ invoice_str_id +'&suppressHeaders=true', { action: 'send_mail_beside', invoice_id: invoice_id, invoice_revision_id: invoice_revision_id, attention_id: attention_id }).dialog({
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
        else {
            $('#'+id).html("Loading ...");
            $('#'+id).load('?m=sales&a=vw_invoice&c=_form_send_mail&suppressHeaders=true', { invoice_id: invoice_id, invoice_revision_id: invoice_revision_id, attention_id: attention_id }).dialog({
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
    
    function setSendButton(invoice_id, invoice_revision_id)
             {
               var id = 'div-popup';
               if (confirm('<?php echo $AppUI->_('Are you sure to send email(s) to customers?'); ?>')) {
                  var sender = $('#sender').val();
                  var reciver = $('#reciver').val();
                  var subject = $('#subject').val();
                  var content = $('#content').val();
                        $.post('?m=sales&a=vw_invoice&c=_do_send_email_invoice&suppressHeaders=true', { action: 'send_mail_in', invoice_id: invoice_id, invoice_revision_id: invoice_revision_id, sender: sender, reciver: reciver, subject: subject, content: content },
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

        function print_invoice(invoice_id, invoice_revision_id, invoice_str_id) {
            if (invoice_id == 0 && invoice_revision_id == 0) { 
                 window.open('?m=sales&a=vw_invoice&c=_do_print_invoice&suppressHeaders=true'+ invoice_str_id,'_blank','');
            } else {
                 window.open('?m=sales&a=vw_invoice&c=_do_print_invoice&suppressHeaders=true&invoice_id='+ invoice_id +'&invoice_revision_id='+ invoice_revision_id,'_blank','');
            }
        }


        function get_invoiceID_checked(id_check, str_check) {
            var inputs = document.getElementsByName(id_check);
            var invoice_id = '';
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                    invoice_id += '&'+str_check+'[]='+inputs[i].value;
                }
            }
            return invoice_id;
        }
        
       
       function add_inline_item() {
            tmp_inv_arr = Array();
            var i;
            var inv_total_item =0;
            //alert($('#invo_total_item').val());
            if($('#invo_total_item').val()!="")
                inv_total_item = $('#invo_total_item').val();
            var count_row = $('#count_row_item').val();
            inv_total_item = parseInt(inv_total_item) + parseInt(count_row);
            var tr = '';
            tr += '<tr id="row_item_'+ count_row +'">';
            tr += '<td align="center"><input type="hidden" id="invoice_order_hdd'+count_row +'" name="invoice_order_hdd[]" value="'+inv_total_item+'">';
            tr += '<img border="0" style="cursor: pointer" onclick="list_template_item('+ count_row +',\'invoice\');return false;" src="images/icons/list.png">';
            tr += '</td>';
            tr += '<td align="center">';
            tr += '<input type="text" class="text" style="text-align:center;" id="invoice_order_'+count_row +'" name="invoice_order[]" value="'+inv_total_item+'" size="4">';
            tr += '</td>';
            tr += '<td>';
            tr += '<textarea type="text" cols="55" rows="4" id="invoice_item_'+ count_row +'" name="invoice_item[]"></textarea>';
            tr += '</td>';
            tr += '<td align="right">';
            tr += '<input type="text" class="text" id="invoice_item_quantity_'+ count_row +'" style="text-align:right;" value="0" name="invoice_item_quantity[]" onchange="load_total_invoice('+ count_row +')" size="8"/>';
            tr += '</td>';
            tr += '<td align="right">';
            tr += '<input type="text" class="text" id="invoice_item_price_'+ count_row +'" style="text-align:right;" value="0.00" name="invoice_item_price[]" onchange="load_total_invoice('+ count_row +')" size="12"/>';
            tr += '</td>';
            tr += '<td align="right">';
            tr += '<input type="text" class="text" id="invoice_item_discount_'+ count_row +'" style="text-align:right;" value="0.00" name="invoice_item_discount[]" onchange="load_total_invoice('+ count_row +')" size="8"/>';
            tr += '</td>';
            tr += '<td>';
            tr += '<img border="0" style="cursor: pointer" onclick="remove_int_item_inline('+ count_row +');" src="images/icons/stock_delete-16.png">';
            tr += '<span id="inv_amount_'+ count_row +'" style="float:right"></span>';
            tr += '<input type="hidden" class="text" id="invoice_item_id" name="invoice_item_id[]" value="0"/>';
            tr += '</td>';
            tr += '</tr>';
             
            inv_item_add_arr.push(count_row);
            $('#add-invoi-inline-foot').append(tr);
            $('#count_row_item').val(parseInt(count_row) + 1);
            
                if(invoice_item_arr.length>0){
                    for(i=0;i < invoice_item_arr.length;i++){
                        //hide_inline(invoice_item_arr[i]);
                        inv_agien(invoice_item_arr[i]);
                    }
                }invoice_item_arr = Array();
            update_invoice_order();
                
        }

        function inv_agien(item_id){
            setTimeout(function(){hide_inline(item_id),0});
        }
        function edit_all() {
            var inputs = document.getElementsByName('item_check_list');
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                    edit_inv_inline(inputs[i].value)
                }
            }
        }
        var click_can=1;
        function edit_inv_inline(item_id) {
            
            if(inv_item_add_arr.length>0){
                alert("Please add all item before edit");
                return;
            }
            var aaa = $('#item_'+item_id).html(); // lay ra item
            var bbb = aaa.replace(/"/g, "&quot;") // replace item sang ky tu dac biet
            var discount = $('#discount_'+item_id).html();
            discount = discount.substr(0, discount.length-1);
            var click_item = parseFloat($('#click_invoice'+item_id).val())+1;
            if(click_item<3){
                ccc = aaa.replace(/\n/g, "");
            }
            //alert(click_item);
            //$('#click_invoice'+item_id).val(parseFloat(click_can)+parseFloat(click_item));
           
                    var tr = '';
                    tr += '<td>';
                    tr += '<a href="#" onclick="hide_inline('+item_id+'); return false;">Cancel</a>';
                    tr += '<input type="hidden" class="text" id="invoice'+item_id+'" name="" value="'+item_id+'"/>';
                    tr += '<input type="hidden" class="text" id="click_invoice'+item_id+'" name="" value="'+click_item+'"/>';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<input type="text" class="text" style="text-align:center;" id="invoice_order_'+item_id+'" name="" value="'+ $('#stt_'+item_id).html() +'" size="4" />';
                    tr += '<input type="text" class="hidden" id="hdd_invoice_order_'+item_id+'" name="" value="'+ $('#stt_'+item_id).html() +'" size="4" />';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<textarea cols="55" rows="4" id="invoice_item_'+item_id+'" name="invoice_item[]" >'+ un_enter_br(ccc) +'</textarea>';
                    tr += '<input type="hidden" class="text" id="invoice_hidden_item_'+item_id+'" name="invoice_hidden_item_[]" value="'+ aaa +'"/>';
                    tr += '</td>';
                    tr += '<td align="right">';
                    tr += '<input type="text" class="text" id="invoice_item_quantity_'+item_id+'" onchange="change_value_item('+item_id+')" style="text-align:right;" name="invoice_item_quantity[]" value="'+ $('#quantity_'+item_id).html() +'" size="8"/>';
                    tr += '<input type="hidden" class="hidden" id="invoice_hidden_item_quantity_'+item_id+'" name="invoice_hidden_item_quantity_[]" value="'+ $('#quantity_'+item_id).html() +'" size="8"/>';
                    tr += '</td>';
                    tr += '<td align="right">';
                    tr += '<input type="text" class="text" id="invoice_item_price_'+item_id+'" style="text-align:right;" onchange="change_value_item('+item_id+')" name="invoice_item_price[]" value="'+ $('#price_'+item_id).html() +'" size="12"/>';
                    tr += '<input type="hidden" class="text" id="invoice_hidden_item_price_'+item_id+'" name="invoice_item_hidden_price[]" value="'+ $('#price_'+item_id).html() +'" size="8"/>';
                    tr += '</td>';
                    tr += '<td align="right">';
                    tr += '<input type="text" class="text" id="invoice_item_discount_'+item_id+'" onchange="change_value_item('+item_id+')" style="text-align:right;" name="invoice_item_discount[]" value="'+ discount +'" size="8"/>';
                    tr += '<input type="hidden" class="text" id="invoice_hidden_item_discount_'+item_id+'" name="invoice_hidden_item_discount_[]" value="'+ discount +'" size="8"/>';
                    tr += '<input type="hidden" class="text" id="invoice_item_id" name="invoice_item_id[]" value="'+item_id+'"/>';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<input type="hidden" class="text" id="invocie_total_'+item_id+'" name="" value="'+ $('#total_'+item_id).html() +'"/>';
                    tr += '<input type="button"  id="invocie_save_'+item_id+'" onclick="save_inv_item('+item_id+');" name="" value="Save"/>';
                    tr += '</td>';
                    $('#row_item_'+item_id).html(tr);
                    $('#click_edit_inv').val(count_inv_click_edit);
                    count_inv_click_edit++;
                    invoice_item_arr.push(item_id);
        }
        function save_inv_item(item_id){
            var invoice_item1 = $('#invoice_item_'+item_id).val();
            var invoice_item = enter_br(invoice_item1);
            var invoice_price = $('#invoice_item_price_'+item_id).val();
                invoice_price = quo_remove_commas(invoice_price);
            var invoice_quantily = $('#invoice_item_quantity_'+item_id).val();
                invoice_price = quo_remove_commas(invoice_price);
            var invoice_discount = $('#invoice_item_discount_'+item_id).val();
            var invoice_id = $('#inv_id').val();
            var invoice_rev_id = $('#inv_rev_id').val();
            var invoice_order = $('#invoice_order_'+item_id).val();
            var total_item = $('#hidden_invoice_item').val();
            
            var check_item = true;
            if(trim(invoice_item1) ==""){
                alert("Please enter item's name");
                check_item = false;
                return;
            }
            else if(is_int(invoice_order) == false){
                alert('Please enter # must is Integer');
                check_item = false;
                return;
            }
            else if(isNaN(invoice_price) == true){
                alert('Please enter Price must is number');
                check_item = false;
                return;
            }
            else if(invoice_price.length > 9){
                alert("Please enter price should be less than 9 digits");
                check_item = false;
                return;
            }
            else if(invoice_quantily <0 || isNaN(invoice_quantily) == true){
                alert('Please enter Quatity must is number and  greater than 0');
                check_item = false;
                return;
            }
            else if(is_int(invoice_quantily) == false){
                alert("Please enter Quantity must is Integer");
                check_item = false;
                return;
            }
            else if(invoice_quantily.length > 9){
                alert("Please enter quantity should be less than 9 digits");
                check_item = false;
                return;
            }
            else if(isNaN(invoice_discount) == true || invoice_discount <0 || invoice_discount >100){
                alert('"Please enter Discount must is number and between 0 and 100"');
                check_item = false;
                return;
            }
            
            if(check_item ==true){
                $.post('?m=sales&a=vw_invoice&c=_do_update_invoice_item&suppressHeaders=true',{item_id:item_id,invoice_item:invoice_item,invoice_price:invoice_price,invoice_quantily:invoice_quantily,invoice_discount:invoice_discount,invoice_order:invoice_order,invoice_id:invoice_id,invoice_rev_id:invoice_rev_id,total_item:total_item},
                    function(data){
                       if(data.status=="Success"){
                           $('#div_inv_3').html('Loading...');
                           $('#div_inv_3').load('?m=sales&a=vw_invoice&c=load_inv_html_table&suppressHeaders=true',{invoice_id:invoice_id,invoice_rev_id:invoice_rev_id});
                           $('#div_inv_total_note').load('?m=sales&a=vw_invoice&c=load_form_inv_total&suppressHeaders=true',{invoice_id:invoice_id,invoice_rev_id:invoice_rev_id});
                            invoice_item_arr = Array();
                            inv_item_add_arr = Array();
                       }
                       else if(data.status=="Failure")
                           {
                               alert(data.message);
                           }
                    },
                "json");
            }
        }
        function hide_inline(item_id) {
                    var item = $('#invoice_hidden_item_'+item_id).val();
                    var click_item =  $('#click_invoice'+item_id).val();
                    var tr = '';
                    tr += '<td align="center">';
                    tr += '<input type="checkbox" value="'+item_id+'" name="item_check_list" id="item_check_list">&nbsp;';
                    tr += '<a href="#" class="icon-edit icon-all" onclick="edit_inv_inline('+item_id+'); return false;" href="#" title="Inline Edit" style="margin-right:0px"></a>';
                    tr += '<a class="icon-save icon-all" onclick="popupFormSaveItemTemplate('+item_id+'); return false;" title="Save item as template" ></a>';
                    tr += '<input type="hidden" class="text" id="click_invoice'+item_id+'" name="" value="'+click_item+'"/>';
                    tr += '</td>';
                    tr += '<td align="center" id="stt_'+item_id+'">';
                    tr += $('#hdd_invoice_order_'+item_id).val();
                    tr += '</td>';
                    tr += '<td id="item_'+item_id+'">';
                    tr += htmlchars(item);
                    tr += '</td>';
                    tr += '<td align="right" id="quantity_'+item_id+'">';
                    tr += $('#invoice_hidden_item_quantity_'+item_id).val();
                    tr += '</td>';
                    tr += '<td align="right" id="price_'+item_id+'">';
                    tr += $('#invoice_hidden_item_price_'+item_id).val();
                    tr += '</td>';
                    tr += '<td align="right" id="discount_'+item_id+'">';
                    tr += $('#invoice_hidden_item_discount_'+item_id).val()+"%";
                    tr += '</td>';
                    tr += '<td align="right" id="total_'+item_id+'">';
                    tr += $('#invocie_total_'+item_id).val();
                    tr += '</td>';
                    $('#row_item_'+item_id).html(tr);
                    count_inv_click_edit1++;
                    $('#click_edit_inv').val(count_inv_click_edit-count_inv_click_edit1);
                    
                    var item_id1 = invoice_item_arr.indexOf(item_id);
                    invoice_item_arr.splice(item_id1,1);
        }
        
        function remove_int_item_inline(key) {
            var rev_tax = $('#invoice_revision_tax').val();
            var a = key.toString();
            var item_id = inv_item_add_arr.indexOf(a);
            inv_item_add_arr.splice(item_id,1);
            tmp_inv_arr = Array();
            $('#row_item_'+ key).remove();
            inv_load_total();
            inv_tax(rev_tax,0);
            var quo_due_total = document.getElementById('inv_total_item').innerHTML;
                quo_due_total = quo_remove_commas(quo_due_total);
            var quo_due_tax=  $('#inv_revision_tax').val();
                quo_due_tax = quo_remove_commas(quo_due_tax);
            var inv_paid = 0;
            if(document.getElementById('inv_paid').innerHTML!=""){
                inv_paid = document.getElementById('inv_paid').innerHTML;
                inv_paid = quo_remove_commas(inv_paid);

            }
            var quo_due = parseFloat(quo_due_total) + parseFloat(quo_due_tax) - parseFloat(inv_paid);
            document.getElementById('inv_due').innerHTML = number_format(quo_due,2,'.',',');
            update_invoice_order();
           //alert(inv_item_add_arr.length);
        }
        function update_invoice_order(){
            var count_row = parseInt($('#invo_total_item').val());
            var t=count_row+1;
            for(var i=0;i<inv_item_add_arr.length;i++){
                $('#invoice_order_'+inv_item_add_arr[i]).val(t);
                t++;
            }
        }
               
        function back_list_invoice(status_id) {
            if(status_id==undefined)
                status_id = 0;
            //window.open('?m=sales&show_all=1&tab=1&s='+status_id,'_self');
            var invoice_no = $('#search-invoice-no').val();
            var contract_no = $('#search-invoice-by-contract').val();
            list_invoice('', status_id, '','', '',invoice_no,contract_no);
        }
        
        function change_quotation_revision(quotation_id, status_rev) {

                var id = 'div-popup';

                    $('#'+id).html("Loading ...");
                    $('#'+id).load('?m=sales&a=vw_invoice&c=change_quotation_revision_html&suppressHeaders=true', { quotation_id: quotation_id, status_rev: status_rev }).dialog({
                                resizable: false,
                                modal: true,
                                title: '<?php echo $AppUI->_('View Quotation'); ?>',
                                width: 450,
                                maxheight: 400,
                                close: function(ev,ui){
                                    $('#'+id).dialog('destroy');
                                }
                    });
                    $('#'+id).dialog('open');
        }
         
        
        <?php //if ($canEdit) { ?>
        $('.invoice_date').editable('?m=sales&a=vw_invoice&c=_do_update_invoice_field&suppressHeaders=true&col_name=invoice_date', {
                    cancel: 'Cancel',
                    submit: 'Save',
                    indicator: 'Loading...'
        });
        <?php //} else { ?>
            //$('.editable_select').click(function(e) {
              //  alert(permissions);
            //});
        <?php //} ?>

        function view_payment(invoice_id, invoice_revision_id){
            window.location = 'index.php?m=sales&a=vw_invoice&c=list_invoice_html&suppressHeaders=true&invoice_id='+ invoice_id +'&invoice_revision_id='+ invoice_revision_id;
        }
        
        function view_history(incoive_id, invoice_revision_id) {
            $.ajax({
                type:"POST",
                url:"?m=sales&a=vw_invoice&c=_do_view_history&suppressHeaders=true", // trang cần xử lý (tức là controller là category có function là search)
                data:"incoive_id="+incoive_id+"&invoice_revision_id="+invoice_revision_id+"&action=view_history", // Lấy dữ liệu của txt gửi đi
                success:function(x){
                    $('#div-popup').html(x).dialog({
                        resizable: true,
                        modal: true,
                        title: '<?php echo $AppUI->_('History'); ?>',
                        width: 500,
                        maxheight: 400,
                        close: function(ev, ui) {
                           $('#div-popup').dialog('destroy');
                        }
                    });
                    $('#div-popup').dialog('open');
                }
        });  
        }
        
        
        function generate_print_invoice(invoice_id, invoice_revision_id) {
            
            if (invoice_id == 0 && invoice_revision_id == 0) { // viec gui mail thuc hien o ngoai danh sach cac invoice
                var invoice_str_id = get_invoiceID_checked('check_list', 'invoice_id');
                if (invoice_str_id != '') {
                    var invoice_split = invoice_str_id.split('&invoice_id[]=');
                    if ((parseInt(invoice_split.length) - 1) == 1) { // neu chon 1 invoice de in pdf, lay mac dinh invoice revision cuoi cung nhat de in
                        $.ajax({
                            type:"POST",
                            url:"?m=sales&a=vw_invoice&c=_do_generate_print&suppressHeaders=true", // trang cần xử lý (tức là controller là category có function là search)
                            data:"quotation_id="+invoice_id+"&quotation_revision_id="+invoice_revision_id, // Lấy dữ liệu của txt gửi đi
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
            //                                alert(generate);
                                            if (generate == 1) {
                                                print_invoice(invoice_id, invoice_revision_id, invoice_str_id);
                                            } else if (generate == 0) {
                                                print_invoice_html(invoice_id, invoice_revision_id, invoice_str_id);
                                            }
                                    }
                                    }
                                });
                                $('#div-popup').dialog('open');
                            }
                    });
                  } else {
                        alert('<?php echo $AppUI->_('Sorry. System allows choose a Invoice to print now.'); ?>');
                  }
          } else {
                alert('<?php echo $AppUI->_('Please select at least one Invoice to print'); ?>');
         }
            
        }else {
            $.ajax({
                    type:"POST",
                    url:"?m=sales&a=vw_invoice&c=_do_generate_print&suppressHeaders=true", // trang cần xử lý (tức là controller là category có function là search)
                    data:"quotation_id="+invoice_id+"&quotation_revision_id="+invoice_revision_id, // Lấy dữ liệu của txt gửi đi
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
    //                                alert(generate);
                                    if (generate == 1) {
                                        print_invoice(invoice_id, invoice_revision_id, invoice_str_id);
                                    } else if (generate == 0) {
                                        print_invoice_html(invoice_id, invoice_revision_id, invoice_str_id);
                                    }
                            }
                            }
                        });
                        $('#div-popup').dialog('open');
                    }
            });  
        }
     }
        function print_invoice_html(invoice_id, invoice_revision_id, invoice_str_id) {
            if (invoice_id == 0 && invoice_revision_id == 0) { 
                window.open('?m=sales&a=vw_invoice&c=_do_print_html&suppressHeaders=true'+ invoice_str_id, '_blank', '');
            } else { 
                window.open('?m=sales&a=vw_invoice&c=_do_print_html&invoice_id='+invoice_id+'&invoice_revision_id='+invoice_revision_id+'&suppressHeaders=true', '_blank', '');
            }
        }
        
// USING TEMPLATE FOR INVOICE

function load_template_invoice(invoice_id, invoice_rev_id, status_rev) {
    $.ajax({
                type: "POST",
                url: "?m=sales&a=vw_invoice&c=_do_getlist_template&suppressHeaders=true",
                data: "status_rev="+status_rev+"&invoice_id="+invoice_id+"&invoice_rev_id="+invoice_rev_id,
                success: function(x) {
                    $('#div-popup').html(x).dialog({
                                    resizable: true,
                                    modal: true,
                                    title: '<?php echo $AppUI->_('List Template'); ?>',
                                    width: 400,
                                    maxheight: 400,
                                    close: function(ev, ui) {
                                        $('#div-popup').dialog('destroy');
                                    },
                                    buttons: {
                                    'Cancel': function() {
                                        $(this).dialog('close');
                                    },
                                    'Use': function() {
                                            var checked = []
                                                $("input[name='check_template[]']:checked").each(function ()
                                                {
                                                    checked.push(parseInt($(this).val()));
                                                    var invoice_id = $('#invoice_id').val();
                                                    var invoice_rev_id = $('#invoice_rev_id').val();
                                                    if (checked == '') {
                                                        alert('please choose Template.');
                                                    } else {
                                                        load_insert_template_items(invoice_id, invoice_rev_id, checked, status_rev);
                                                    }
                                                });
                                        }
                                    }
                                });
                            $('#div-popup').dialog('open');
                }
                
             });
}

function load_insert_template_items(invoice_id, invoice_revision_id, template, status_rev) {
            var mang_giatri = $('#frm_all_invoice').serialize();
            mang_giatri = mang_giatri+'&invoice_id='+ invoice_id +'&invoice_revision_id='+ invoice_revision_id +'&status_rev='+ status_rev+'&template_id='+template;
            $.post('?m=sales&a=vw_invoice&c=_do_insert_template_invoice&suppressHeaders=true', mang_giatri,
                    function(data) {
                        if (data.status == 'Success') {
                            $('#div-popup').dialog('close');
                                load_invoice(data.invoice_id, data.invoice_revision_id, status_rev);
                        } else if (data.status == "Failure") {
                            alert(data.message);
                        }
                    }, "json");
         }
         
          function insert_term_condition(template_id){
            $.ajax({
                type: "POST",
                url: "?m=sales&a=vw_tab_term&c=_load_term_condition&suppressHeaders=true&template_term_id="+template_id,
                data: "",
                success: function(x) {
                    $('#div-popup').html(x).dialog({
                                    resizable: true,
                                    modal: true,
                                    title: '<?php echo $AppUI->_('List Term & Conditon'); ?>',
                                    width: 400,
                                    maxheight: 400,
                                    close: function(ev, ui) {
                                        $('#div-popup').dialog('destroy');
                                    },
                                    buttons: {
                                    'Cancel': function() {
                                        $(this).dialog('close');
                                    },
                                    'Insert': function() {
                                            var checked = []
                                                $("input[name='check_template[]']:checked").each(function ()
                                                {
                                                    checked.push(parseInt($(this).val()));
                                                    var current_textarea = $('#invoice_revision_term_condition').val()+"\n";
                                                    var textAreaAttitude = $(this).val();
                                                        if (textAreaAttitude != '') {
                                                            $('#invoice_revision_term_condition').val(current_textarea + textAreaAttitude);
                                                            $('#div-popup').dialog('close');
                                                        } else {
                                                            alert('Please choose');
                                                        }
                                                });
                                        }
                                    }
                                });
                            $('#div-popup').dialog('open');
                }
                
             });
        }
        function getTextArea() {
            var checked = []
            $("input[name='check_template[]']:checked").each(function (i)
            {
//                checked.push(parseInt($(this).val()));
//                term_val = $('#check_template').val()+'\n';
                  checked[i] = $(this).val();
            });
        var textAreaAttitude = checked;
        return textAreaAttitude;
    }
    function load_email(attention_id,act){
        
        if(act!=0){
            $('#inv_email_'+act).html('');
            $('#inv_email_'+act).load('?m=sales&a=vw_invoice&c=_get_email&suppressHeaders=true',{attention_id:attention_id});
            $('#sel_title_'+act).load('?m=sales&a=vw_invoice&c=get_title_contact&suppressHeaders=true',{attention_id:attention_id});
        }else{
            $('#inv_email').html('');
            $('#inv_email').load('?m=sales&a=vw_invoice&c=_get_email&suppressHeaders=true',{attention_id:attention_id});
            $('#sel_title').load('?m=sales&a=vw_invoice&c=get_title_contact&suppressHeaders=true',{attention_id:attention_id});
        }
    }
function load_address(customer_id) { //(Anhnn)
        var invoice_id = $('#inv_id').val();
        var jobLocation_id = $('#inv_job_location_id').val();
        $('#sel_address').load('?m=sales&a=vw_invoice&c=_get_address_select&suppressHeaders=true', { customer_id: customer_id});
        $('#sel_job_location').load('?m=sales&a=vw_invoice&c=_get_job_address_select&suppressHeaders=true', { customer_id: customer_id});
        $('#inv_client_address').html('');
        $('#inv_email').html('');
        $('#inv_client_address').load('?m=sales&a=vw_invoice&c=_get_client_address&suppressHeaders=true', { customer_id: customer_id});
        $('#inv_email').load('?m=sales&a=vw_invoice&c=_get_email&suppressHeaders=true', { customer_id: customer_id});
        $('#contact_job').html('');
        $('#inv_contact_id').html('<option value="">--Select--</option>');
        $('#inv_address_contact').html('');
        $('#title_customer').load('?m=sales&a=vw_invoice&c=_get_title_customer&suppressHeaders=true',{customer_id:customer_id});
        $('#inv_contract_no').load('?m=sales&a=vw_invoice&c=load_contract_no&suppressHeaders=true',{customer_id:customer_id,invoice_id:invoice_id,job_location_id:jobLocation_id});
        
        load_cash2();
        $('#sel_title').load('?m=sales&a=vw_invoice&c=get_title_contact&suppressHeaders=true',{customer_id:customer_id});
        $('#inv_attention_id').load('?m=sales&a=vw_invoice&c=_get_attention_select&suppressHeaders=true', { customer_id: customer_id});
        //$('#add_line_attn_foot tr').remove();
    }
function load_client_address(address_id) { //(Anhnn)    
                $('#inv_client_address').load('?m=sales&a=vw_invoice&c=_get_client_address&suppressHeaders=true', {address_id: address_id});
    }


    function load_invoice_revision(invoice_no){
        var invoice_revision = $('#invoice_revision').val();
        $('#int_rev').html('Loading...');
        $('#int_rev').load('?m=sales&a=vw_invoice&c=load_invoiceRev_get_invoiceNo&suppressHeaders=true',{invoice_no:invoice_no,invoice_revision:invoice_revision});
    }
    function cancel_invoice(){
        $('#div_inv_details').html('Loading...');
        $('#div_inv_details').load('?m=sales&a=vw_invoice&suppressHeaders=true');
    }
    
    function load_total_invoice(item){
        var rev_tax = $('#invoice_revision_tax').val();
        var price = 0, quanlity= 0,discount=0, tax_dis = 0 ;
        var item_price = $('#invoice_item_price_'+item).val();
        var item_qty = $('#invoice_item_quantity_'+item).val();
        var item_dis = $('#invoice_item_discount_'+item).val();
        //var item_total = $('#hidden_invoice_item').val();
        var total = new Array();
        if(trim(item_price)!="")
            price = item_price;
        if(trim(item_qty)!="")
            quanlity = item_qty;
        if(trim(item_dis)!="")
            discount = item_dis;
        // Kiem tra tinh hop le cua gia tri truyen vao
        var check = true;
            if(isNaN(price) == true){
                alert('Please enter Price must is number');
                check = false;
                $('#invoice_item_price_'+item).val("");
                price = 0;
            }
            if(price.length > 9){
                alert("Please enter Price should be less than 9 digits");
                check = false;
                $('#invoice_item_price_'+item).val("");
                price= 0;
            }
            if(quanlity < 0 || isNaN(quanlity) == true){
                alert('Please enter Quatity must is number and  greater than 0');
                check = false;
                $('#invoice_item_quantity_'+item).val("");
                quanlity= 0;
                
            }
            if(is_int(quanlity) == false){
                alert('Please enter Quatity must is Integer');
                check = false;
                $('#invoice_item_quantity_'+item).val("");
                quanlity= 0;
                
            }
            if(quanlity.length > 9){
                alert("Please enter quantity should be less than 9 digits");
                check = false;
                $('#invoice_item_quantity_'+item).val("");
                quanlity= 0;
            }
            if(isNaN(discount) == true || discount<0 || discount >100){
                alert('"Please enter Discount must is number and between 0 and 100"');
                check = false;
                $('#invoice_item_discount_'+item).val("");
                discount = 0;
            }
            var are_item = parseFloat(price) * parseInt(quanlity);
            if(trim(item_dis)!=""){
                tax_dis = parseFloat(are_item)*(parseFloat(discount)/100);
            }
            var item_amount = parseFloat(are_item) - parseFloat(tax_dis);
            document.getElementById('inv_amount_'+item).innerHTML = number_format(round_up(item_amount),2,'.',',');

            inv_load_total();
            inv_tax(rev_tax,0);
            var quo_due_total = document.getElementById('inv_total_item').innerHTML;
                quo_due_total = quo_remove_commas(quo_due_total);
            var quo_due_tax=  $('#inv_revision_tax').val();
                quo_due_tax = quo_remove_commas(quo_due_tax);
            var inv_paid = 0;
            if(document.getElementById('inv_paid').innerHTML!=""){
                inv_paid = document.getElementById('inv_paid').innerHTML;

                inv_paid = quo_remove_commas(inv_paid);
            }
            var quo_due = parseFloat(quo_due_total) + parseFloat(quo_due_tax) - parseFloat(inv_paid);
            document.getElementById('inv_due').innerHTML = number_format(quo_due,2,'.',',');
    }
    
    function inv_load_total(){
        var item_total =0;
        if($('#hidden_invoice_item').val()>0){
            var item_total = $('#hidden_invoice_item').val();
            item_total = quo_remove_commas(item_total);
        }
        var tmp = 0;
        for(var i=1;i<=inv_item_add_arr.length;i++){
            tmp_inv_arr[i-1]=quo_remove_commas(document.getElementById('inv_amount_'+inv_item_add_arr[i-1]).innerHTML);
            tmp_inv_arr[i-1] = round_up(parseFloat(tmp_inv_arr[i-1]));
            tmp = parseFloat(tmp)+parseFloat(tmp_inv_arr[i-1]);
            tmp = tmp.toFixed(2);

        }
        var total = parseFloat(tmp) +  parseFloat(item_total);
        document.getElementById('inv_total_item').innerHTML = number_format(total,2,'.',',');
    }
    function inv_tax(value,change){
        var total = $('#hidden_invoice_item').val();
        var total_quo = document.getElementById('inv_total_item').innerHTML;
        var discount = $('#invoice_revision_discount').val();
        if(discount =="")
            discount = 0;
        total_quo = quo_remove_commas(total_quo);
        var total_last_discount = parseFloat(total_quo) - parseFloat(discount);
        var invoice_revision_tax_edit = "0.00";
        if(value!=0){
            var rate = $("#invoice_revision_tax :selected").text();
//            alert(rate);
            var total_tax = parseFloat(total_last_discount)*parseFloat(rate/100);
            invoice_revision_tax_edit = number_format(round_up(total_tax),2,'.',',');
        }
        // kiem tra total co thay doi ko de thuc hien update tax
        if(parseFloat(total_quo)!=parseFloat(total) || change==1)
            $('#inv_revision_tax').val(invoice_revision_tax_edit);
        // End
        var quo_due_total = document.getElementById('inv_total_item').innerHTML;
        quo_due_total = quo_remove_commas(quo_due_total);
        var quo_due_tax=  $('#inv_revision_tax').val();
        quo_due_tax = quo_remove_commas(quo_due_tax);
        var inv_paid = 0;
        if(document.getElementById('inv_paid').innerHTML!=""){
            inv_paid = document.getElementById('inv_paid').innerHTML;
            inv_paid = quo_remove_commas(inv_paid);
        }
        var quo_due = parseFloat(total_last_discount) + parseFloat(quo_due_tax) - parseFloat(inv_paid);
        document.getElementById('inv_due').innerHTML = number_format(quo_due,2,'.',',');

    }
    function load_payment_invoice(customer_id){
        window.open('?m=sales&show_all=1&tab=3&status=add&customer_id='+customer_id,'_self');       
    }
function load_update_term_invoice(){
     
      if ($("#invoice_id_hd").length){
          $.post('?m=sales&a=vw_invoice&c=_do_update_status_invoice1&suppressHeaders=true&invoice_id='+$("#invoice_id_hd").val()+'&invoice_save_id='+$("#invoice_id_hd").val(),
            function(data){
            if(data.status == 'Success')
                var a="thanh cong";
    //        else if(data.status == 'Failure')
    //            alert(data.status_id);
         },"json");
      }
//      else
//      {
//      $.post('?m=sales&a=vw_invoice&c=_do_update_status_invoice1&suppressHeaders=true',
//        function(data){
//        if(data.status == 'Success')
//            var a="thanh cong";
////        else if(data.status == 'Failure')
////            alert(data.status_id);
//     },"json");
//    }
}
function enter_br(str){
        for(var i=0;i<str.length;i++)
            str = str.replace('\n','<br>');
        return htmlchars(str);
}
function un_enter_br(str){
        for(var i=0;i<str.length;i++)
            str = str.replace('<br>','\n');
        return str;
}
function quo_remove_commas(str){
    str=str.toString();
    for(var i=0;i<str.length;i++){
        var str = str.replace(",","");                   
    }
    return str;
}
function test_email(email) {
                var str = email;
                var at = "@"
                var dot = "."
                var lat = str.indexOf(at)
                var lstr = str.length
                var ldot = str.indexOf(dot)

                if (str.indexOf(at)==-1) return false
                if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr) return false
                if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr) return false
                if (str.indexOf(at,(lat+1))!=-1) return false
                if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot) return false
                if (str.indexOf(dot,(lat+2))==-1) return false
                if (str.indexOf(" ")!=-1) return false
                var re = /^([A-Za-z0-9\_\-]+\.)*[A-Za-z0-9\_\-]+@[A-Za-z0-9\_\-]+(\.[A-Za-z0-9\_\-]+)+$/;
                if(str.search(re)==-1) return false
                return true;
}
function htmlchars(str){
    if(typeof(str) == "string"){
        str = str.replace(/&/g, "&amp;");
        str = str.replace(/"/g, "&quot;");
        str = str.replace(/'/g, "&#039;");
        str = str.replace(/<(?!br)/g, "&lt;");
        //str = str.replace(/>/g, "&gt;");
    }
    return str;
}
function _get_contact_jobLocation(jobLocation_id){
        var invoice_id = $('#inv_id').val();
        var customer_id = $('#inv_customer_id').val();
    $('#inv_contact_id').load('?m=sales&a=vw_invoice&c=_get_contact_jobLocation&suppressHeaders=true&job_location_id='+jobLocation_id);
    $('#inv_contract_no').load('?m=sales&a=vw_invoice&c=load_contract_no&suppressHeaders=true',{customer_id:customer_id,invoice_id:invoice_id,job_location_id:jobLocation_id});
}
function load_email_contact(contact_id){
    $('#inv_address_contact').load('?m=sales&a=vw_invoice&c=_get_email&suppressHeaders=true',{contact_id:contact_id});
}
function addContactAddress() {
//  $('#address_contact_id').val(address_id);
//  popupAddnew('frm_add_brand_new_contact','Add Account');   
    var customer_id = $('#inv_customer_id').val();
    var address_id = $('#inv_job_location_id').val();
    if(customer_id==""||customer_id==undefined){
        alert("Please Provide client name.");
        return;
    }
    else if(address_id==""||address_id==undefined){
        alert("Please select Job Location.");
        return;
    }
//    $.ajax({
//        url:'?m=sales&a=frmNewContact&suppressHeaders=true',
//        data: {client_id: customer_id, address_id: address_id , status: "invoice"},
//        type: 'POST',
//        success:function(data){
//            $('#div-popup').html(data);
//            popupAddnew('div-popup', 'Add Account');
//        }
//   });
      var id = 'div-popup';
      $('#'+id).load('?m=sales&a=frmNewContact&suppressHeaders=true', {client_id: customer_id, address_id: address_id , status: "invoice"}).dialog({
            resizable: false,
            modal: true,
            title: '<?php echo $AppUI->_('Add Account'); ?>',
            width: 600,
            close: function(ev,ui){
                $('#'+id).dialog('destroy');
            }
            });
            $('#'+id).dialog('open');
   
                        
}
function addContactAttention() {
//  $('#address_contact_id').val(address_id);
//  popupAddnew('frm_add_brand_new_contact','Add Account');   
    var customer_id = $('#inv_customer_id').val();
    var address_id = $('#inv_address_id').val();
    if(customer_id==""||customer_id==undefined){
        alert("Please Provide client name.");
        return;
    }
    else if(address_id==""||address_id==undefined){
        alert("Please select Address.");
        return;
    }
      var id = 'div-popup';
      $('#'+id).load('?m=sales&a=frmNewContact&suppressHeaders=true', { client_id: customer_id, address_id: address_id , status: "invoice", att_status:"attention" }).dialog({
            resizable: false,
            modal: true,
            title: '<?php echo $AppUI->_('Add Account'); ?>',
            width: 600,
            close: function(ev,ui){
                $('#'+id).dialog('destroy');
            }
            });
            $('#'+id).dialog('open');
                        
}
function load_action(value){
    var invoice_id = $('#inv_id').val();
    var invoice_rev_id = $('#inv_rev_id').val();
    var countRev = $('#countRev').val();
    if(value=="Change Revision"){
        change_revision(invoice_id,invoice_rev_id,"update");
    }
    else if(value == "History"){
        view_history(invoice_id,invoice_rev_id);
    }
    else if(value == "Delete this revision"){
        delete_invoice_revision(invoice_id,invoice_rev_id,countRev);
    }
    else if(value == "Delete this Invoice")
    {
        delete_one_invoice(invoice_id);
    }
}
function addNewAddress() {
//            var client_id = <?php echo $_GET['company_id'];?>;
//                $.ajax({
//                    url:'?m=clients&a=frmAddress&suppressHeaders=true',
//                    data: {client_id: client_id},
//                    type: 'POST',
//                    success:function(data){
//                        $('#form_addnew_address').html(data);
//                    }
//                });
//            popupAddnew('form_addnew_address', 'New Address');
    var client_id = $('#inv_customer_id').val();
    if(client_id==""||client_id==undefined){
        alert("Please Provide client name.");
        return;
    }
      var id = 'div-popup';
      $('#'+id).load('?m=sales&a=frmAddress&suppressHeaders=true', { client_id: client_id, status: "invoice" }).dialog({
            resizable: false,
            modal: true,
            title: '<?php echo $AppUI->_('New Address'); ?>',
            width: 600,
            close: function(ev,ui){
                $('#'+id).dialog('destroy');
            }
            });
            $('#'+id).dialog('open');
}
function load_creditNote(customer_id,address_id,invoice_id){
    window.open('?m=sales&show_all=1&tab=2&status=add&customer_id='+customer_id+'&address_id='+address_id+'&invoice_id='+invoice_id+'&action=1','_self');
}

function load_invoice_no_exist(){
    //alert("123");
    $.post('?m=sales&a=vw_invoice&c=_do_load_invoice_no_exist&suppressHeaders=true',{acation:"exits"},
    function(data){
        $('#invoice_no').val(data.sales_invoice_no);
        $('#invoice_revision').val(data.sales_invoice_rev);
    },"json");
}

function add_inline_attention(){
    var customer_id = $('#inv_customer_id').val();
    if(customer_id==""){
        alert("Please select customer.");
        return false;
    }
    var count_attn = $('#count_attn').val();
    var html='<tr valign="top">\n\
                <td><input type="hidden" name="attn_id[]" value="0" /></td>\n\
                <td>\n\
                    <span id="sel_title_'+count_attn+'"></span>\n\
                    <span id="sel_attention_'+count_attn+'">\n\
                        <select id="inv_attention_id_'+count_attn+'" class="text_select" onchange="load_email(this.value,'+count_attn+');" style="width:319px" name="attention_id[]"></select>\n\
                    </span>\n\
                    <div style="margin-top:5px;" id="inv_email_'+count_attn+'"></div>\n\
                </td>\n\
                <td>&nbsp;</td>\n\
            </tr>';
    $('#add_line_attn_foot').append(html);
    $('#sel_title_'+count_attn).load('?m=sales&a=vw_invoice&c=get_title_contact&suppressHeaders=true',{customer_id:customer_id,act_attn:1});
    $('#inv_attention_id_'+count_attn).load('?m=sales&a=vw_invoice&c=_get_attention_select&suppressHeaders=true', { customer_id: customer_id,act_attn:1});
    $('#count_attn').val(parseInt(count_attn)+1);
}
function load_cash2(){
    var table ='<table border="0" width="100%">\n\
        <tbody>\n\
            <tr valign="top">\n\
                <td width="19.5%" style="padding-top:5px;">\n\
                    Attention:<a class="icon-add icon-all" onclick="add_inline_attention(); return false;" style="margin-right:0px;" href="#"></a>\n\
                    <input id="onchange_customer" type="hidden" value="1" name="onchange_customer">\n\
                </td>\n\
                <td width="62%">\n\
                    <span id="sel_title">\n\
                        <select id="contacts_title_id" class="text" size="1" style="height:20pt;" name="contacts_title_id[]"></select>\n\
                    </span>\n\
                    <span id="sel_attention">\n\
                        <select id="inv_attention_id" class="text_select" onchange="load_email(this.value,0);" style="width:319px" name="attention_id[]"></select>\n\
                    </span>\n\
                    <div id="inv_email" style="margin-top:5px;"></div>\n\
                </td>\n\
                <td>\n\
                    <button class="ui-button ui-state-default ui-corner-all" onclick="addContactAttention(); return false;">Add contact</button>\n\
                </td>\n\
            </tr>\n\
        </tbody>\n\
        <tfoot id="add_line_attn_foot"><input id="count_attn" type="hidden" value="1" name="count_attn"></tfoot>\n\
    </table>';
    $('#div_2_cash').html(table);
    //$('#div_2_cash').html("");
}
function load_apply_customer(){
    var ischeck = ischeck_radio("check_customer");
    var address = $('#txt_key_address').val();
    if(address==""){
        alert('Please enter the address to search customer.');
        return false;
    }
    if(ischeck==false){
        alert('Please choose customer to apply.');
        return false;
    }
    var customer_id = $('#check_customer:checked').val();
    $('#sel_customer').load('?m=sales&a=vw_invoice&c=_get_load_customer&suppressHeaders=true',{customer_id:customer_id});
    load_address(customer_id);
    $('#div-popup').dialog('destroy');
}
function change_value_item(item_id)
{

    // Kiem tra xem co thay doi gia tri item ko de update tax
   change_value = 1;
}
function save_confirm(invoice_id,invoice_revision_id)
{
    $(".loading_active").fadeIn();
    $.post('?m=sales&a=vw_invoice&c=_do_confirm_invoice',{invoice_id:invoice_id,invoice_revision_id:invoice_revision_id},
        function(data)
        {
            load_invoice(invoice_id,invoice_revision_id, 'update');
        }
    );
}

function searchInvoiceBycontract()
{
    var contract_no = $('#search-invoice-by-contract').val();
    var status_id = $('#invoice_status_id').val();
    if(contract_no.length>1 || contract_no.length==0)
    {
        $('#div-list-invoice').html('Loading...');
        $('#div-list-invoice').load('?m=sales&a=vw_invoice&c=list_invoice_html&suppressHeaders=true',{contract_no:contract_no,status_id:status_id});
    }
}

function search_invoice_by_invoice_no(invoice_no)
{
    if(invoice_no.length>1 || invoice_no.length==0)
        list_invoice('', '', '','', '',invoice_no);
}

function delete_one_invoice(invoice_id)
{
    var invoice_status = $('#status_inv_id').val();
    if(invoice_status != 5){
        alert("You are only allowed to delete a Draft invoice.");
        return;
    }

    if (confirm('<?php echo $AppUI->_('Are you sure delete record(s)?'); ?>')) {
        $.post('?m=sales&a=vw_invoice&c=_do_remove_invoice&invoice_id[]='+ invoice_id +'&suppressHeaders=true', {  },
            function(data) {
                if (data.status == 'Success') {
                    back_list_invoice(invoice_status);
                    $.post('?m=sales&a=vw_invoice&c=_do_inv_update_done_sevice_order&invoice_id[]='+ invoice_id +'&suppressHeaders=true', {  },
                        function(data){

                        },"json");
                } else if (data.status == "Failure") {
                    alert(data.message);
                }
        }, "json");
    }
}
function search_invoice()
{
    var status = $('#invoice_status_id').val();
    var invoice_no = $('#search-invoice-no').val();
    var contract_no = $('#search-invoice-by-contract').val();
    
    list_invoice('',status,'','','',invoice_no);
    $('#button_new').css({display:'block'}); 
}

function load_discount()
{
    var rev_tax = $('#invoice_revision_tax').val();
    inv_tax(rev_tax,1);
}

</script>
