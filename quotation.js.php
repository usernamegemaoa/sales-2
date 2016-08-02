<script type="text/javascript">
        var count_click_edit1=1;
        var count_click_edit=1;
        var quotation_item_arr = new Array();
        var quo_item_add_array = new Array();
        var quotation_item_arr1 = new Array();
        var tmp_arr = new Array();
        var change_value = 0;
        var count =$('#quotation_count').val();
         
    $(document).ready(function() {
                loadJQueryCalendar('quotation_date_display', 'quotation_date', 'dd/mm/yy', 'yy-mm-dd');     
                 
                $('#quotation_sortable tr').css({'cursor': 'pointer'});

                $('#quotation_sortable tr').click(function () {    
                    console.log('click');
                    
                }); 
                var tr = '<tfoot id="add-quo-inline-foot">';
                tr += '<input type="hidden" id="count_row_item" value="'+(++count)+'">';
                tr += '</tfoot>';
                $('#detail_quotation_table').append(tr);
                
                var search_status_id = $('#quotation_status_id').val();
                var search_quotation_no = $('#search-quotation-no').val();
                var search_department = $('#department').val();
                
                $('#quotation_table').dataTable({
                    'sprocessing':true,
                    'bServerSide':true,
                    "bDestroy": true,
                    "border": [[ 0, "desc" ]],
                    'sAjaxSource':'?m=sales&a=server_processing_quotation&quotation_no='+search_quotation_no+'&status_id='+search_status_id+'&department_id='+search_department+'&suppressHeaders=true',
                    'aoColumns':[
                        {"align": "left","asSorting": [ "desc" ]},
                        {"align": "left"},
                        {"align": "left"},
                        {"align": "left"},
                        {"align": "left"},
                        {"sClass": "text_right"},
                        {"sClass": "text_center"},
                    ],
                    "fnDrawCallback": function( oSettings ) {
                        $('.quotation_status').editable('?m=sales&a=vw_quotation&c=_do_update_quotation_field&suppressHeaders=true&col_name=quotation_status', {
                                indicator    : 'Loading...',
                                width: 220,
                                loadurl : '?m=sales&a=vw_quotation&c=get_quotation_status&suppressHeaders=true',
                                type: 'select',
                                submit: 'Save',
                                cancel: 'Cancel',
                                height: 20
                            });
                    }
                });
                
                var oTable = $('#detail_quotation_table').dataTable();
                
                $('#detail_quotation_table_filter label input[type=text]').click(function(){
                   oTable.search( $(this).val('item') ).draw();
                });

                
                $('#div_quotation_info input').css({'float':'right','width':'200px'});
                $('#div_quotation_info select').css({'float':'right'});
                $('#div_quotation_info p').css({'overflow':'hidden','padding-bottom':'5px'});
                $('#div_2_right p').css({'overflow':'hidden','padding-bottom':'5px'});
                $('#div_2_right input').css({'float':'right','margin-right':'0px','width':'200px'});
                $('#div_2_client p').css({'padding-bottom':'5px'});
                $('#quotation_status_id').css({});
                
                ///
                $('#customer_id').select2({
                    placeholder: "Select a Customer",
                });
                $('#quotation_co').select2({placeholder: "Select a CO"});
                $('#address_id').select2({placeholder: "Select a Address"});
                $('#job_location_id').select2({placeholder: "Select a Job location"});
                $('#sel_customer .select2-container').css({'top':'8px'});
                $('.select2-container').css({'width':'393px'});
                $('#quo_sel_address .select2-container').css({'width':'500px'});
                $('#quo_sel_location .select2-container').css({'width':'500px'});
                
    });

        function list_quotation(customer_id, status_id, status, quotation_id, quotation_revision_id,dept_id,quotation_no) {
            var quo_id = $('#quo_id').val();
            var filters = "";
            if(quo_id==undefined)
            {
                $('#stt-block').css({'display':'none'});
            }
            else
            {
                filters = $('#filter_search').val();
                $('#stt-block').css({'display':'block'});
            }
            if(dept_id==undefined || dept_id=="")
                dept_id = $('#department').val();
            else if(status_id==undefined || status_id=="")
                status_id = $('#quotation_status_id').val();
            quotation_no = $('#search-quotation-no').val();
            $('#div-list-quotation').html('Loading...');
            $('#div-list-quotation').load('?m=sales&a=vw_quotation&c=list_quotation_html&suppressHeaders=true', { customer_id: customer_id, status_id: status_id, dept_id:dept_id, quotation_no:quotation_no },function(){
                $('#quotation_table_filter label input[type=text]').val(filters).trigger($.Event("keyup", { keyCode: 13 }));
            });
            $('#sales_task_popup-editTask').remove();
            //$('#div_task').html('<div id="sales_task_popup-editTask" style="display: none;"></div>');
        }
        function load_quotation(quotation_id, quotation_revision_id, status_rev) {
            var dept_id = 0;
            var filter = $('#quotation_table_filter label input[type=text]').val();
            if(status_rev=="add")
            {
                dept_id = $('#department').val();
                if(dept_id=="")
                {
                    alert("Please select one derparment.");
                    return false;
                }
            }
            var quo_id = $('#quo_id').val();
            if(quo_id==undefined)
            {
                $('#stt-block').css({'display':'none'});
            }
            else
            {
                $('#stt-block').css({'display':'block'});
            }
            
            // Kiem tra xem derpart ment lua chon co chua user ko?
            $.ajax({
                type:"POST",
                url:"?m=sales&a=vw_quotation&c=isUserInDepartment&suppressHeaders=true",
                data:{dept_id:dept_id},
                success:function(data){
                    var response = jQuery.parseJSON(data);
                    if(response.status=="success")
                    {
                        var status_back = $('#quotation_status_id').val();
                        $('#div-list-quotation').html('Loading...');
                        $('#div-list-quotation').load('?m=sales&a=vw_quotation&c=view_quotation_detail&suppressHeaders=true', { quotation_id: quotation_id, quotation_revision_id: quotation_revision_id, status_rev: status_rev,status_back:status_back,dept_id:dept_id,filter:filter });
                    }
                    else if(response.status=="faild")
                    {
                        alert("You are not allowed to add this quotation because you are not a member of this department.");
                    }
                },
            });
        }

        function change_revision(quotation_id, quotation_revision_id, status_rev) {

                    var id = 'div-popup';

                    $('#'+id).html("Loading ...");
                    $('#'+id).load('?m=sales&a=vw_quotation&c=change_revision_html&suppressHeaders=true', { quotation_id: quotation_id, quotation_revision_id: quotation_revision_id, status_rev: status_rev }).dialog({
                                resizable: false,
                                modal: true,
                                title: '<?php echo $AppUI->_('List Revision'); ?>',
                                width: 450,
                                maxheight: 400,
                                close: function(ev,ui){$('#'+id).dialog('destroy');}
                    });
                    $('#'+id).dialog('open');
        }

        function new_item_quotation(quotation_id, quotation_revision_id, status_rev) {
                var id_poup = 'div-popup';

                $('#'+id_poup).html("<font color='#f87217'><i>Loading ...</i></font>");
        $('#'+id_poup).load('?m=sales&a=vw_quotation&c=_popup_new_item&suppressHeaders=true', { quotation_id: quotation_id, quotation_revision_id: quotation_revision_id, status_rev: status_rev }).dialog({
                            resizable: false,
                            modal: true,
                            title: '<?php echo $AppUI->_('New Item'); ?>',
                            width: 450,
                            maxheight: 'auto',
                            close: function(ev,ui){$('#'+id_poup).dialog('destroy');}
                });
                $('#'+id_poup).dialog('open');

        }

//        $("#loading").dialog({
//            hide: 'slide',
//            show: 'slide',
//            autoOpen: false
//        });
        
        
        function save_all_quotation(quotation_id, quotation_revision_id, status_rev, template) {
            
            
            var quotation_rev = $('#quotation_revision').val();
            var revision_lastest = $('#revision_lastest').val();     
            var quotation_no = $('#quotation_no').val();
            quotation_no=quotation_no.trim();
            var customer_id = $('#customer_id').val();
            var quotation_sale_person = $('#quotation_sale_person').val();
            var quotation_sale_person_email = $('#quotation_sale_person_email').val();
            var quotation_sale_person_phone = $('#quotation_sale_person_phone').val();
            var count_total_item = $('#count_total_item').val();
            var count_row_item = $('#count_row_item').val();
            var quotation_item_price = new Array();
            var quotation_item_quantit = new Array();
            var quotation_item = new Array();
            var quotation_item_discount = new Array();
            var quotation_status_id=$('#quotation_status_id').val();
            var check_perm = $('#perm_edit').val();
            var check = true;
            if(quotation_status_id==3 && check_perm!=1){
                alert('Sorry.You can not update this  Approved Quotation');
                check = false;
                return;
            }
            if(trim(quotation_no)==""){ 
                alert ('Sorry. This Quotation No is empty');
                check = false;
                return;
            }
            if(no_special(quotation_no) == false){
                alert ('Sorry. This Quotation No enter special characters.');
                check = false;
                return;
            }
            if(customer_id == "" || customer_id == 0){
                alert('Please provide client name');
                check = false;
                return;
            }
//            if(quotation_sale_person == "" || quotation_sale_person_email == "" || quotation_sale_person_phone == ""){
//                alert ('Please provide Sales contact');
//                check = false;
//                return;
//            }
            if(trim(quotation_sale_person_email)!="" && test_email(quotation_sale_person_email) == false){
                alert ('Invalid Email');
                check = false;
                return;
            }
            if(isNaN(quotation_sale_person_phone)==true){
                alert ('Phone must is number');
                check = false;
                return;
            }
            if(count_total_item == "" && count_row_item<=1){
                alert("Please enter at  least a Item's detail");
                check = false;
                return;
            }
            var check_item = 0;
            var quotation_arrr_item="";
            for(i=1;i<=quo_item_add_array.length;i++){
                var j= quo_item_add_array[i-1];
                var quotation_price = $('#quotation_item_price_'+j).val();
                quotation_item_price[i] = quotation_price.replace(/,/g, "");
                quotation_item_quantit[i] = $('#quotation_item_quantity_'+j).val();
                quotation_item[i] = $('#quotation_item_'+j).val();
                quotation_arrr_item += '&quotation_arrr_item[]='+enter_br(quotation_item[i]);

//                alert(quotation_item[i]);
//                return
                quotation_item_discount[i] = $('#quotation_item_discount_'+j).val();
                if(trim(quotation_item[i]) =="")
                    check_item = 1;
                else if(isNaN(quotation_item_price[i]) == true){
                    check_item = 2; 
                }
                else if(quotation_item_quantit[i] <0 || isNaN(quotation_item_quantit[i]) == true)
                    check_item = 3;
                else if(isNaN(quotation_item_discount[i]) == true || quotation_item_discount[i] <0 || quotation_item_discount[i] >100)
                    check_item = 4;
            }
            if(check_item == 1){
                alert("Please enter item's name");
                check = false;
                return;
            }
            if(check_item == 2){
                alert('Please enter Price must is number');
                check = false;
                return;
            }
            if(check_item == 3){
                alert('Please enter Quatity must is number and  greater than 0');
                check = false;
                return; 
            }
            if(check_item == 4){
                alert('"Please enter Discount must is number and between 0 and 100"');
                check = false;
                return;
            }
            
            if(check == true){
                var mang_giatri = $('#frm_all_quotation').serialize();
                
                
                    mang_giatri = mang_giatri+'&quotation_id='+ quotation_id +'&quotation_revision_id='+ quotation_revision_id +'&status_rev='+ status_rev+quotation_arrr_item;//+'&quotation_template='+template_id
                if(status_rev == 'update_no_rev') {
                    $('#btn-save-all_add').removeAttr("onclick");
                    $(".loading_active").fadeIn();
                   
                    $.post('?m=sales&a=vw_quotation&c=_do_add_quotation&suppressHeaders=true', mang_giatri,
                        function(data) {
                            if (data.status == 'Success') {
                                if(data.exist_item==1)
                                    alert("Adding item failed. Item with the exact same description and amount already exists");
                                $("#loading").dialog('close');
                                load_quotation(data.quotation_id, data.quotation_revision_id, status_rev);
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                        }, "json");
                }
                else if (status_rev == 'update'){    //Neu la update quotation_revision thi hien thong bao (Anhnn)
                    if (confirm('You are saving an OLDER revision '+ quotation_rev +' . But your latest revision is '+ revision_lastest +'. Are you sure?')) {
                        $('#btn-save-all-revision').removeAttr("onclick");
                        $(".loading_active").fadeIn();
                        $.post('?m=sales&a=vw_quotation&c=_do_add_quotation&suppressHeaders=true', mang_giatri,
                        function(data) {
                            if (data.status == 'Success') {
//                                $("#loading").dialog('close');
                                load_quotation(data.quotation_id, data.quotation_revision_id, status_rev);
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                        }, "json");
                    }
                }
                else
                {
                    $('#btn-save-all_add').removeAttr("onclick");
                    $(".loading_active").fadeIn();

                    $.post('?m=sales&a=vw_quotation&c=_do_add_quotation&suppressHeaders=true', mang_giatri,
                    function(data) {
//                        alert("123123");
                        var template_id = $('#quotation_template').val();
                        if (data.status == 'Success') {
                            if(!template_id) {
                                $("#loading").dialog('close');
                                load_quotation(data.quotation_id, data.quotation_revision_id, status_rev);
                            }
//                            else {
//                                load_template_items(data.quotation_id, data.quotation_revision_id, template_id, status_rev);
//                            }
                        } else if (data.status == "Failure") {
                            alert(data.message);
                            load_quotation_no_exist();
                        }
                    }, "json");
                }
                var mang_giatri1 = $('#frm_all_quotation').serialize();
                $.post('?m=contacts&a=ajax_edit_contact_title&suppressHeaders=true',mang_giatri1);
            }
        }

        
        function delete_quotation(quotation_id, quotation_revision_id) {
       
            var quotation_str_id = get_quotationID_checked('check_list_quo', 'quotation_id');
            var quotation_status = new Array();
            
            var inputs = document.getElementsByName("check_list_quo");
            var id = '';
            var check =true;
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                    id =inputs[i].value;
                    quotation_status[i] = $('#quotation_status_'+id).val();
                    if(quotation_status[i] == 3){
                        check = false;
                    }
                }
            }
            if(check == false){
                alert("You can not delete beacause this quotation was approved");
                return;
            }
//           alert(inputs.length);
           else if (quotation_str_id != '') {
                if (confirm('<?php echo $AppUI->_('Are you sure to delete record(s)?'); ?>')) {
                    $.post('?m=sales&a=vw_quotation&c=_do_remove_quotation'+ quotation_str_id +'&suppressHeaders=true', {  },
                        function(data) {
                            if (data.status == 'Success') {
                                list_quotation('', $('#quotation_status_id ').val());
                                $.post('?m=sales&a=vw_quotation&c=_do_update_done_sevice_order'+ quotation_str_id +'&suppressHeaders=true', {  },
                                    function(data){
                                        
                                    },"json");
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one Quotation to delete'); ?>');
            }
        }
        
        function delete_quotation_item(quotation_id, quotation_revision_id, status_rev) {
            var quotation_no = $('#quotation_no').val();
            var quotation_str_id = get_quotationID_checked('item_check_list', 'quotation_item_id');
            if (quotation_str_id != '') {
                if (confirm('<?php echo $AppUI->_('Are you sure to delete record(s)?'); ?>')) {
                    $.post('?m=sales&a=vw_quotation&c=_do_remove_quotation_item'+ quotation_str_id +'&suppressHeaders=true', { quotation_id: quotation_id, quotation_revision_id: quotation_revision_id, status_rev: status_rev,quotation_no:quotation_no },
                        function(data) {
                            if (data.status == 'Success') {
                                load_quotation(data.quotation_id, data.quotation_revision_id, status_rev);
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one Quotation to delete'); ?>');
            }
        }

        function delete_quotation_revision(quotation_id, quotation_revision_id, countRevision) {
            var quotation_status_id = $('#quotation_status_id').val();
            if(countRevision<=1){
                alert("you can not Delete all the Quotation Revesion in 1 Quotation");
                return;
            }
            if(quotation_status_id == 3){
                alert("You can not delete beacause this quotation was approved");
                return;
            }
            if (confirm('<?php echo $AppUI->_('Are you sure to delete record(s)?'); ?>')) {
                if (quotation_revision_id != '' && quotation_revision_id != null) {
                    $.post('?m=sales&a=vw_quotation&c=_do_remove_quotation_revision&suppressHeaders=true', {quotation_id:quotation_id, quotation_revision_id: quotation_revision_id },
                        function(data) {
                            if (data.status == 'Success') {
                                load_quotation(data.quotation_id, data.quotation_rev_id,'update')
                            } else if (data.status == "Failure") {
                                alert(data.message);
                            }
                    }, "json");
                }
            }
        }

        function edit_quotation_item(quotation_id, quotation_revision_id, status_rev) {
            var quotation_item_str_id = get_quotationID_checked('item_check_list', 'quotation_item_id');
            if (quotation_item_str_id != '') {
                var quotation_item_split = quotation_item_str_id.split('&quotation_item_id[]=');
                    if ((parseInt(quotation_item_split.length) - 1) == 1) {
                        $('#'+id_poup).html("<font color='#f87217'><i>Loading ...</i></font>");
                        $('#'+id_poup).load('?m=sales&a=vw_quotation&c=_popup_new_item&suppressHeaders=true'+ quotation_item_str_id, { quotation_id: quotation_id, quotation_revision_id: quotation_revision_id, status_rev: status_rev }).dialog({
                                    resizable: false,
                                    modal: true,
                                    title: '<?php echo $AppUI->_('Edit Item'); ?>',
                                    width: 450,
                                    maxheight: 'auto',
                                    close: function(ev,ui){$(this).html('');}
                        });
                        $('#'+id_poup).dialog('open');
                    } else {
                        alert('<?php echo $AppUI->_('Ban chi duoc phep chon 1 quotation item'); ?>');
                    }
            } else {
                    alert('<?php echo $AppUI->_('Please select at least one Quotation item to edit'); ?>');
            }
        }

        function load_quotation_rev(quotation_id) {
            var id_content = $('#div-quotation-rev-'+quotation_id).html();
            if (id_content == '') {
                $('#div-quotation-rev-'+quotation_id).hide()
                $('#div-quotation-rev-'+quotation_id).html('loading...');

                $('#div-quotation-rev-'+quotation_id).load('?m=sales&a=vw_quotation&c=change_revision_html&suppressHeaders=true', { quotation_id: quotation_id })

            }
        }

        function quotation_rev_more(quotation_id) {
            $('#div-quotation-rev-'+quotation_id).toggle('slow');
            //if ($('#div-quotation-rev-'+quotation_id).hide() == true)
              //  $('#quotation-rev-more-'+quotation_id).html('(+)');
            //if ($('#div-quotation-rev-'+quotation_id).show() == true)
              //  $('#quotation-rev-more-'+quotation_id).html('(-)');
        }

        function email_quotation(quotation_id, quotation_revision_id, contact_id) {
            if (quotation_id == 0 && quotation_revision_id == 0) { // viec gui mail thuc hien o ngoai danh sach cac quotation
                var quotation_str_id = get_quotationID_checked('check_list_quo', 'quotation_id');
                if (quotation_str_id != '') {
                    if (confirm('<?php echo $AppUI->_('Are you sure to send email(s) to customers?'); ?>')) {
                        $.post('?m=sales&a=vw_quotation&c=_do_send_email_quotation'+ quotation_str_id +'&suppressHeaders=true', { action: 'send_mail_beside' },
                            function(data) {
                                alert(data.message);
                        }, "json");
                    }
                } else {
                    alert('<?php echo $AppUI->_('Please select at least one Quotation to send email'); ?>');
                }
            } else { // gui mail thuc hien khi view 1 quotation revision
                    if (confirm('<?php echo $AppUI->_('Are you sure to send email(s) to customers?'); ?>')) {
                        $.post('?m=sales&a=vw_quotation&c=_do_send_email_quotation&suppressHeaders=true', { action: 'send_mail_in', quotation_id: quotation_id, quotation_revision_id: quotation_revision_id },
                            function(data) {
                                alert(data.message);
                        }, "json");
                        window.location.href = 'index.php?m=sales&a=vw_quotation&c=_form_send_mail&suppressHeaders=true&quotation_id='+ quotation_id +'&quotation_revision_id='+ quotation_revision_id;
                    }
            }
        }


function print_quotation(quotation_id, quotation_revision_id, quotation_str_id) {
            if (quotation_id == 0 && quotation_revision_id == 0) { // viec gui mail thuc hien o ngoai danh sach cac quotation
                   window.open ('?m=sales&a=vw_quotation&c=_do_print_quotation&suppressHeaders=true'+ quotation_str_id, '_blank','');
               } else { // gui mail thuc hien khi view 1 quotation revision
                  var check_show = document.getElementById("option_show").checked;
                  var show=0;
                  if(check_show==true)
                      show=1;
                  window.open ('?m=sales&a=vw_quotation&c=_do_print_quotation&suppressHeaders=true&quotation_id='+ quotation_id +'&quotation_revision_id='+ quotation_revision_id+'&show='+show,'_blank','');
            }
   }


        function get_quotationID_checked(id_check, str_check) {
            var inputs = document.getElementsByName(id_check);
            var quotation_id = '';
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                    quotation_id += '&'+str_check+'[]='+inputs[i].value;
                }
            }
            return quotation_id;
        }
        
//        function list_quotationID_checked(id_check, str_check) {
//            var inputs = document.getElementsByName(id_check);
//            var quotation_id = '';
//            for (var i = 0; i < inputs.length; i++) {
//                if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
//                    quotation_id += '&'+str_check+'[]='+inputs[i].value;
//                }
//            }
//            return quotation_id;
//        }
        
        function add_inline_item(status_id,quotation_id) {
           
            
            tmp_arr = Array();
            var i;
            var total_item = 0;
            
            var count_row = $('#count_row_item').val();
            if(status_id == 3)
            {
                viewContractItem(status_id,quotation_id);
            }
            else
            {
            total_item = parseInt(total_item) + parseInt(count_row);
            var tr = '';
            tr += '<tr id="row_item_'+ count_row +'">';
            tr += '<td align="center"><input type="hidden" id="hdd_quotation_order_'+count_row +'" name="hdd_quotation_order[]" value="'+total_item+'">';
            tr += '<img border="0" style="cursor: pointer" onclick="list_template_item('+ count_row +',\'quotation\');return false;" src="images/icons/list.png">';
            tr += '</td>';
            tr += '<td>';
            tr += '<input type="text" style="text-align:center;" class="text" id="quotation_order_'+count_row +'" name="quotation_order[]" value="'+total_item+'" size="4" />';
            tr += '</td>';
            tr += '<td>';
            tr += '<textarea cols="55"  rows="4" width="100" id="quotation_item_'+ count_row +'" name="quotation_item[]" ></textarea>';
            tr += '</td>';
            tr += '<td align="right" width="200">';
            tr += '<input type="text" class="text" id="quotation_item_quantity_'+ count_row +'" style="text-align:right;" name="quotation_item_quantity[]" size="8" value="0" onchange="load_total_quotation('+count_row+');"/>';
            tr += '</td>';
            tr += '<td align="right" width="80">';
            tr += '<input type="text" class="text" id="quotation_item_price_'+ count_row +'" name="quotation_item_price[]" style="text-align:right;" value="0.00" onchange="load_total_quotation('+count_row+');" size="12"/>';
            tr += '</td>';
            tr += '<td align="right" width="80">';
            tr += '<input type="text" class="text" id="quotation_item_discount_'+ count_row +'" name="quotation_item_discount[]" style="text-align:right;" size="8" value="0.00" onchange="load_total_quotation('+count_row+');"/>';
            tr += '</td>';
            tr += '<td width="80">';
            tr += '<img border="0" style="cursor: pointer" onclick="remove_item_inline('+ count_row +');" src="images/icons/stock_delete-16.png">';
            tr += '<span id="quo_amount_'+count_row+'" style="float:right"></span>';
            tr += '<input type="hidden" class="text" id="quotation_item_id" name="quotation_item_id[]" value="0"/>';
            tr += '</td>';
            tr += '</tr>';
            quo_item_add_array.push(count_row);
//            alert(quo_item_add_array);
            $('#add-quo-inline-foot').append(tr);
            $('#count_row_item').val(parseInt(count_row) + 1);
            }
//            if(quotation_item_arr.length>0){
//                for(i=0;i < quotation_item_arr.length;i++){
//                   //hide_inline(quotation_item_arr[i]);
//                   quo_agien(quotation_item_arr[i]);
//                }
//                
//            }quotation_item_arr = Array();
//            update_quotation_order();
        }
        function quo_agien(item_id){
            setTimeout(function(){hide_inline(item_id)},0);
        }

        function edit_all() {
            var inputs = document.getElementsByName('item_check_list');
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                    edit_inline(inputs[i].value)
                }
            }
        }
        
        
        function edit_inline(item_id) {
            if(quo_item_add_array.length>0){
                alert("Please add all item before edit");
                return;
            }
                
            var aaa = $('#item_'+item_id).html(); // lay ra item
            var bbb = aaa.replace(/"/g, "&quot;") // replace item sang ky tu dac biet
            var discount = $('#discount_'+item_id).html();
            discount = discount.substr(0, discount.length-1);
            var click_item = parseFloat($('#click_quotation'+item_id).val())+1;
            if(click_item<3){
                ccc = aaa.replace(/\n/g, "");
            }
                    var tr = '';
                    tr += '<td>';
                    tr += '<a href="#" onclick="hide_inline('+item_id+'); return false;">Cancel</a>';
                    tr += '<input type="hidden" class="text" id="quotation_'+item_id+'" name="" value="'+item_id+'"/>';
                    tr += '<input type="hidden" class="text" id="click_quotation'+item_id+'" name="" value="'+click_item+'"/>';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<input type="text" style="text-align:center;" class="text" id="quotation_order_'+item_id +'" name="quotation_order[]" value="'+$('#stt_'+item_id).html()+'" size="4" />';
                    tr += '<input type="hidden" class="text" id="quotation_stt_'+item_id+'" name="" value="'+ $('#stt_'+item_id).html() +'"/>';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<textarea cols="55" rows="4"   id="quotation_item_'+item_id+'" name="quotation_item[]" >'+un_enter_br(ccc)+'</textarea>';
                    tr += '<input type="hidden" class="text" id="quotation_hidden_item_'+item_id+'" name="quotation_hidden_item[]" value="'+ aaa +'"/>';
                    tr += '</td>';
                    tr += '<td align="right">';
                    tr += '<input type="text" class="text" style="text-align:right;" id="quotation_item_quantity_'+item_id+'" onchange="change_value_item('+item_id+')" name="quotation_item_quantity[]" value="'+ $('#quantity_'+item_id).html() +'" size="8"/>';
                    tr += '<input type="hidden" class="text" id="quotation_hidden_item_quantity_'+item_id+'" name="quotation_hidden_item_quantity_[]" value="'+ $('#quantity_'+item_id).html() +'" size="8"/>';
                    tr += '</td>';
                    tr += '<td align="right">';
                    tr += '<input type="text" class="text" style="text-align:right;" id="quotation_item_price_'+item_id+'" onchange="change_value_item('+item_id+')" name="quotation_item_price[]" value="'+ $('#price_'+item_id).html() +'" size="12"/>';
                    tr += '<input type="hidden" class="text" id="quotation_hidden_item_price_'+item_id+'" name="quotation_hidden_item_price_[]" value="'+ $('#price_'+item_id).html() +'" size="8"/>';
                    tr += '</td>';
                    tr += '<td align="right">';
                    tr += '<input type="text" class="text" style="text-align:right;" id="quotation_item_discount_'+item_id+'" onchange="change_value_item('+item_id+')" name="quotation_item_discount[]" value="'+ discount +'" size="8"/>';
                    tr += '<input type="hidden" class="text" id="quotation_hidden_item_discount_'+item_id+'" name="quotation_hidden_item_discount_[]" value="'+ discount +'" size="8"/>';
                    tr += '<input type="hidden" class="text" id="quotation_item_id" name="quotation_item_id[]" value="'+item_id+'"/>';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<input type="hidden" class="text" id="quotaiton_total_'+item_id+'" name="" value="'+ $('#total_'+item_id).html() +'"/>';
                    tr += '<input type="button" id="quotaiton_save_'+item_id+'" onclick="save_edit_quo_item('+item_id+');" name="" value="Save"/>';
                    tr += '</td>';
                    $('#row_item_'+item_id).html(tr);
                    $('#click_edit_quo').val(count_click_edit);
                    count_click_edit++;
                    quotation_item_arr.push(item_id);
                    quotation_item_arr1.push(item_id);
                    
        }
        
        function save_edit_quo_item(item_id){
            var quotation_id = $('#quo_id').val();
            var quotation_rev_id = $('#quo_rev_id').val();
            var quotation_item1 = $('#quotation_item_'+item_id).val();
            var quotation_item = enter_br(quotation_item1);
            var quotation_item_quantity = $('#quotation_item_quantity_'+item_id).val();
            var quotation_item_order = $('#quotation_order_'+item_id).val();
                quotation_item_quantity = quo_remove_commas(quotation_item_quantity);
            var total_item = $('#hidden_quototal_item').val();
            //quotation_item_quantity = quotation_item_quantity.substr(quotation_item_quantity.length-1, quotation_item_quantity.length)
            
            var quotation_item_price = $('#quotation_item_price_'+item_id).val();
                quotation_item_price = quo_remove_commas(quotation_item_price);
                quotation_item_price = quotation_item_price.replace(/,/g, "")
            var quotation_item_discount = $('#quotation_item_discount_'+item_id).val();
            var check_item = true;
            if(is_int(quotation_item_order)==false){
                alert('Please enter # must is integer');
                check_item = false;
                return;
            }
            if(trim(quotation_item1) ==""){
                alert("Please enter item's name");
                check_item = false;
                return;
            }
            else if(isNaN(quotation_item_price) == true){
                alert('Please enter Price must is number');
                check_item = false;
                return;
            }
            else if(quotation_item_quantity <0 || isNaN(quotation_item_quantity) == true){
                alert('Please enter Quatity must is number and  greater than 0');
                check_item = false;
                return;
            }
            else if(isNaN(quotation_item_discount) == true || quotation_item_discount <0 || quotation_item_discount >100){
                alert('"Please enter Discount must is number and between 0 and 100"');
                check_item = false;
                return;
            }
            
            
            if(check_item==true){
                $.post('?m=sales&a=vw_quotation&c=_do_update_quotation_item&suppressHeaders=true',{item_id: item_id,quotation_id: quotation_id,quotation_rev_id:quotation_rev_id,quotation_item:quotation_item,quotation_item_quantity: quotation_item_quantity, quotation_item_price: quotation_item_price,quotation_item_discount: quotation_item_discount,quotation_order:quotation_item_order,total_item:total_item},
                function(data){
                    if(data.status == 'Success'){
                        $('#div_3').html('Loading...');
                        $('#div_3').load('?m=sales&a=vw_quotation&c=load_html_table&suppressHeaders=true',{quotation_id:quotation_id,quotation_rev_id:quotation_rev_id});
                        $('#div_total_note').load('?m=sales&a=vw_quotation&c=load_form_total&suppressHeaders=true',{quotation_id:quotation_id,quotation_rev_id:quotation_rev_id});
                        quotation_item_arr = Array();
                        quotation_item_arr1 = Array();
                        quo_item_add_array = Array();
                    }
                    if (data.status == 'Failure') {
                        alert(data.message);
                    }
                },"json");
            }
        }
        
        function change_value_item(item_id)
        {
          
            // Kiem tra xem co thay doi gia tri item ko de update tax
           change_value = 1;
        }
        
        function hide_inline(item_id) {
                    var item = $('#quotation_hidden_item_'+item_id).val();
                    var click_item =  $('#click_quotation'+item_id).val();
                    var tr = '';
                    tr += '<td align="center">';
                    tr += '<input type="checkbox" value="'+item_id+'" name="item_check_list" id="item_check_list">&nbsp;';
                    tr += '<a href="#" class="icon-edit icon-all" title="Edit item" onclick="edit_inline('+item_id+'); return false;" href="#" title="Inline Edit" style="margin-right:0px"></a>';
                    tr += '<a class="icon-save icon-all" onclick="popupFormSaveItemTemplate('+item_id+'); return false;" title="Save item as template" ></a>';
                    tr += '<input type="hidden" class="text" id="click_quotation'+item_id+'" name="" value="'+click_item+'"/>';
                    tr += '</td>';
                    tr += '<td align="center" id="stt_'+item_id+'">';
                    tr += $('#quotation_stt_'+item_id).val();
                    tr += '</td>';
                    tr += '<td id="item_'+item_id+'">';
                    tr += htmlchars(item);
                    tr += '</td>';
                    tr += '<td align="right" id="quantity_'+item_id+'">';
                    tr += $('#quotation_hidden_item_quantity_'+item_id).val();
                    tr += '</td>';
                    tr += '<td align="right" id="price_'+item_id+'">';
                    tr += $('#quotation_hidden_item_price_'+item_id).val();
                    tr += '</td>';
                    tr += '<td align="right" id="discount_'+item_id+'">';
                    tr += $('#quotation_hidden_item_discount_'+item_id).val()+'%';
                    tr += '</td>';
                    tr += '<td align="right" id="total_'+item_id+'">';
                    tr += $('#quotaiton_total_'+item_id).val();
                    tr += '</td>';
                    $('#row_item_'+item_id).html(tr);
                    count_click_edit1++;
                    $('#click_edit_quo').val(count_click_edit-count_click_edit1);
                    

                    var item_id1 = quotation_item_arr.indexOf(item_id);
                    quotation_item_arr.splice(item_id1,1);
                    //quotation_item_arr.shift();
                    //quotation_item_arr1.shift();
        }
        
        function remove_item_inline(key) {
            var rev_tax = $('#quotation_revision_tax').val();
            var a = key.toString();
            var item_id = quo_item_add_array.indexOf(a);
            quo_item_add_array.splice(item_id,1);
            tmp_arr = Array();
            $('#row_item_'+ key).remove();
            quo_load_total();
            quo_tax(rev_tax,0);
            var quo_due_total = document.getElementById('quo_total_item').innerHTML;
                quo_due_total = quo_remove_commas(quo_due_total);
            var quo_due_tax= $('#quo_revision_tax').val();
                quo_due_tax= quo_remove_commas(quo_due_tax);
            var quo_due = parseFloat(quo_due_total) + parseFloat(quo_due_tax);
            document.getElementById('quo_due').innerHTML = number_format(quo_due,2,'.',',');
            var count_row_item = parseInt($('#count_row_item').val());
            //$('#count_row_item').val(count_row_item-1);
            update_quotation_order();
        }
        
        function update_quotation_order(){
            var count_row = parseInt($('#quotation_count').val())+1;
       
            var t=count_row;
            for(var i=0;i<quo_item_add_array.length;i++){
                $('#quotation_order_'+quo_item_add_array[i]).val(t);
                t++;
            }
            //alert(quo_item_add_array.length);
        }
     
//Convert quotation into Invoice (Anhnn)
        function convert_quotation_into_invoice(quotation_id, quotation_revision_id,invoice_id) {
            // Neu da ton tai 1 invoice thi se hien ra 1 dialog hoi muon co them invoice nua ko
            $.ajax({
                                        'type':'POST',
                                        'url':'?m=sales&a=vw_quotation&c=get_so_by_quotation&suppressHeaders=true',
                                        'data':{quotation_id: quotation_id},
                                        success:function(data){
                                                $("#show_so").html(data).dialog({
                                                    resizable: true,
                                                    modal: true,
                                                    title: '<?php echo $AppUI->_('Bạn có muốn gán invoice nào vào Service Order ?'); ?>',
                                                    width: 500,
                                                    maxheight: 400,
                                                    close: function(ev, ui) {
                                                       $('#show_so').dialog('destroy');
                                                    }
                                                });
                                                $('#show_so').dialog('open');
                                        }
                                    
                                    });
        }    
//Convert quotation into Invoice - END
    function closesobyquotation() {
        $('#show_so').dialog('close');
    }

    function update_invoice_by_so(serviceorder) {
        alert(serviceorder);
    }
    
    //COPY quotation  (Anhnn)  
    function copy_quotation(quotation_id, quotation_revision_id) {
            if (quotation_id == 0 && quotation_revision_id == 0) {
                var inputs = document.getElementsByName('check_list_quo');
                var count_checked = 0;
                for (var i = 0; i < inputs.length; i++) {
                    if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                        count_checked ++;
                    }
                }
                if(count_checked >1 ){
                    alert('choose only one quotation to copy');
                    return;
                }
                var quotation_str_id = get_quotationID_checked('check_list_quo', 'quotation_id');
                if (quotation_str_id != '') {
                    if (confirm('<?php echo $AppUI->_('Are you sure to copy this quotation?'); ?>')) {
                        $.post('?m=sales&a=vw_quotation&c=_do_copy_quotation'+ quotation_str_id +'&suppressHeaders=true', { action: 'copy_beside'},
                            function(data) {
                                alert(data.message);
                                list_quotation('', '');
                        }, "json");
                    }
                } else {
                    alert('<?php echo $AppUI->_('Please select at least one quotation'); ?>');
                }
            } else { 
                    if (confirm('<?php echo $AppUI->_('Are you sure to copy this quotation?'); ?>')) {
                        $.post('?m=sales&a=vw_quotation&c=_do_copy_quotation&suppressHeaders=true', { action: 'copy_in', quotation_id: quotation_id, quotation_revision_id: quotation_revision_id },
                            function(data) {
                                alert(data.message);
                                list_quotation('', '');
                        }, "json");
                    }
            }
        }
    //END    

/*
*  view history update quotation and qoutation revisions 
*  dungnv 10/08/2012
 */
 
 function view_history(quotation_id, quotation_revision_id) {
// if (quotation_id != '') {
        $.ajax({
                type:"POST",
                url:"?m=sales&a=vw_quotation&c=_do_view_history&suppressHeaders=true", // trang cần xử lý (tức là controller là category có function là search)
                data:"quotation_id="+quotation_id+"&quotation_revision_id="+quotation_revision_id+"&action=view_history", // Lấy dữ liệu của txt gửi đi
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
//     }
 }

function update_status(quotation_id,quotation_status, status_update) { // Update Quotation status khi vao xem chi tiet 1 quotation
            var customer_id = $('#customer_id').val();
            var job_location_id = $('#job_location_id').val();
            var quotation_revision_id = $('#quo_rev_id').val();
            var user_type ='<?php echo $AppUI->user_type;?>';
            if(quotation_status == 3 || status_update ==3){
               
            $.ajax({
                       type:"POST",
                       url:'?m=sales&a=vw_quotation&c=_do_update_quotation_field&suppressHeaders=true&col_name=quotation_status&status=update_view_status', // trang cần xử lý (tức là controller là category có function là search)
                       data:{id:quotation_id, value: quotation_status, act:"act",quotation_revision_id:quotation_revision_id,status_update:status_update}, // Lấy dữ liệu của txt gửi đi
                       success:function(data){
                          $('#quotation_status').load('?m=sales&a=vw_quotation&c=select_status&suppressHeaders=true',{quotation_id : quotation_id});
                          if(user_type != 1 && (status_update ==3))
                               alert(data);
                          else
                          {
                              reload_form_button();
                               load_quotation(quotation_id, quotation_revision_id, 'update');
                          }
                       }
               });
                $('#quotation_status').load('?m=sales&a=vw_quotation&c=select_status&suppressHeaders=true',{quotation_id : quotation_id});
                return;
            }
            
            var sevice_order = $('#quotation_serviceoder').val();
            //alert(sevice_order);
                if(sevice_order==""){
                   
                    if (confirm('<?php echo $AppUI->_('Are you sure to update this quotation status?'); ?>')) {
                        $.post('?m=sales&a=vw_quotation&c=_do_update_quotation_field&suppressHeaders=true&col_name=quotation_status&status='+status_update, { id: quotation_id, value: quotation_status, act:"act",quotation_revision_id:quotation_revision_id},
                            function(data) {   
                               
                                 $('#quotation_status').load('?m=sales&a=vw_quotation&c=select_status&suppressHeaders=true',{quotation_id : quotation_id});
                                 reload_form_button();
                                 load_quotation(quotation_id, quotation_revision_id, 'update');
                        }, "json");
                    }
                    else
                    {
                       
                        $('#quotation_status').load('?m=sales&a=vw_quotation&c=select_status&suppressHeaders=true',{quotation_id : quotation_id});
                        
                    }
                }else{
                    if (confirm('<?php echo $AppUI->_('Are you sure to update this quotation status?'); ?>')) {
                        $.post('?m=sales&a=vw_quotation&c=_do_update_quotation_field&suppressHeaders=true&col_name=quotation_status&status='+status_update, { id: quotation_id, value: quotation_status, act:"act",quotation_revision_id:quotation_revision_id},
                            function(data) {                                
                                if(quotation_status==3){
                                    
                                    if (confirm('<?php echo $AppUI->_('Do you want to create the respective job for this quotation now?'); ?>')) {
                                        loadTaskEdit_quo(0,quotation_id,customer_id,job_location_id);
                                        load_quotation(quotation_id, quotation_revision_id, 'update');
                                        reload_form_button();
                                    }
                                }
                                else if(quotation_status==4){
                                    sales_done_serviceoder(sevice_order,quotation_id,"quotation");
                                }
                        }, "json");
                    }
//                    if(quotation_status==3){
//                        if (confirm('<?php echo $AppUI->_('add job?'); ?>')) {
//                            loadTaskEdit_quo(0,quotation_id,customer_id,job_location_id);
//                        }
//                    }
//                    else if(quotation_status==4){
//                        alert(sevice_order);
//                        sales_done_serviceoder(sevice_order);
//                    }
                }
}

        function back_list_quotation() {
            $('#div_details').html('Loading...');
            $('#div_details').load('?m=sales&a=vw_quotation&suppressHeaders=true');
        }
        function back_quo_contracts(status_id){
            search_quotation();
            //window.open('?m=sales&show_all=1&tab=0&s='+status_id+'&d='+dept_id,'_self');
        }
        function change_invoice_revision(invoice_id, status_rev) {

                var id = 'div-popup';

                    $('#'+id).html("Loading ...");
                    $('#'+id).load('?m=sales&a=vw_quotation&c=change_invoice_revision_html&suppressHeaders=true', { invoice_id: invoice_id, status_rev: status_rev }).dialog({
                                resizable: false,
                                modal: true,
                                title: '<?php echo $AppUI->_('View Invoice'); ?>',
                                width: 450,
                                maxheight: 400,
                                close: function(ev,ui){$('#'+id).dialog('destroy');}
                    });
                    $('#'+id).dialog('open');
        }
        
    
        $(document).ready(function() {
            var tr = '<tfoot id="add-inline-foot">';
            tr += '<input type="hidden" id="count_row_item" value="1">';
            tr += '</tfoot>';
            $('#detail_quotation_table').append(tr);
        });        
        

        <?php //if ($canEdit) { ?>
        $('.quotation_date').editable('?m=sales&a=vw_quotation&c=_do_update_quotation_field&suppressHeaders=true&col_name=quotation_date', {
                    cancel: 'Cancel',
                    submit: 'Save',
                    indicator: 'Loading...'
        });
        <?php //} else { ?>
            //$('.editable_select').click(function(e) {
              //  alert(permissions);
            //});
                        
        <?php //} ?>
function load_address(customer_id) { //(Anhnn)
        var quotation_id = $('#quo_id').val();
        var jobLocation_id = $('#job_location_id').val();
        $('#quo_sel_address').load('?m=sales&a=vw_quotation&c=_get_address_select&suppressHeaders=true', { customer_id: customer_id});
        
        $('#quo_sel_location').load('?m=sales&a=vw_quotation&c=_get_job_location_select&suppressHeaders=true', { customer_id: customer_id});
        $('#client_address').load('?m=sales&a=vw_quotation&c=_get_client_address&suppressHeaders=true', { customer_id: customer_id});
        //$('#email').load('?m=sales&a=vw_quotation&c=_get_email_detail&suppressHeaders=true', { customer_id: customer_id});
        $('#email').load('?m=sales&a=vw_quotation&c=_get_email&suppressHeaders=true',{customer_id:customer_id});
        $('#contact_job').html('');
        $('#contact_id').html('<option value="">--Select--</option>');
        $('#address_contact').html('');
        $('#title_customer').load('?m=sales&a=vw_quotation&c=_get_title_customer&suppressHeaders=true',{customer_id:customer_id});
        $('#quo_contract_no').load('?m=sales&a=vw_quotation&c=load_contract_no&suppressHeaders=true',{customer_id:customer_id,quotation_id:quotation_id,job_location_id:jobLocation_id});
        
        load_quo_cash2();
        $('#sel_title').load('?m=sales&a=vw_invoice&c=get_title_contact&suppressHeaders=true',{customer_id:customer_id});
        $('#attention_id').load('?m=sales&a=vw_quotation&c=_get_attention_select&suppressHeaders=true', { customer_id: customer_id});
    }

function load_client_address(address_id) { //(Anhnn)    
                $('#client_address').load('?m=sales&a=vw_quotation&c=_get_client_address&suppressHeaders=true', {address_id: address_id});
    }
        
    function popupSendEmail(quotation_id, quotation_revision_id){
        var id = 'div-popup';
        var attention_id = $('#attention_id').val();

        if (quotation_id == 0 && quotation_revision_id == 0){
            var quotation_str_id = get_quotationID_checked('check_list_quo', 'quotation_id');
            $('#'+id).html("Loading ...");
            $('#'+id).load('?m=sales&a=vw_quotation&c=_form_send_mail'+ quotation_str_id +'&suppressHeaders=true', { action: 'send_mail_beside', quotation_id: quotation_id, quotation_revision_id: quotation_revision_id, attention_id: attention_id }).dialog({
            resizable: false,
            modal: true,
            title: '<?php echo $AppUI->_('Send email'); ?>',
            width: 700,
            maxheight: 400,
            close: function(ev,ui){$('#'+id).dialog('destroy');}
            });
            $('#'+id).dialog('open');
        }
        else {
            $('#'+id).html("Loading ...");
            $('#'+id).load('?m=sales&a=vw_quotation&c=_form_send_mail&suppressHeaders=true', { quotation_id: quotation_id, quotation_revision_id: quotation_revision_id, attention_id: attention_id }).dialog({
            resizable: false,
            modal: true,
            title: '<?php echo $AppUI->_('Send email'); ?>',
            width: 700,
            maxheight: 400,
            close: function(ev,ui){$('#'+id).dialog('destroy');}
            });
            $('#'+id).dialog('open');
        }
    }

    
    function closeDialogI(id){
        var id_='#'+id;
        //$(id_).dialog('close');
        $(id_).dialog('close');
    }
    
    function insertCells(r)
        {
        var i=r.parentNode.parentNode.rowIndex;
        var x=document.getElementById("myTable").insertRow(i+1);
        var z=x.insertCell(0);
        var y=x.insertCell(1);
          y.innerHTML="<input type=\'file\' name=\'file3[]\'  id=\'1\'> <a href=\'#\' onclick=\'javascript:deleteRows(this)\'>Delete<a>";
        if(document.getElementById)
        {
        document.getElementById("file1").style.display="none"; 
        document.getElementById("file2").style.display=""; 
        document.getElementById("1").className="insert1";
        }
        }
        
        function setSendButton(quotation_id, quotation_revision_id)
             {
               var id = 'div-popup';
               if (confirm('<?php echo $AppUI->_('Are you sure to send email(s) to customers?'); ?>')) {
                  var sender = $('#sender').val();
                  var reciver = $('#reciver').val();
                  var subject = $('#subject').val();
                  var content = $('#content').val();
                        $.post('?m=sales&a=vw_quotation&c=_do_send_email_quotation&suppressHeaders=true', { action: 'send_mail_in', quotation_id: quotation_id, quotation_revision_id: quotation_revision_id, sender: sender, reciver: reciver, subject: subject, content: content },
                            function(data) {
                                if (data.status == 'Success'){
                                    alert(data.message);
                                    $('#'+id).dialog('close');
                                }
                            else if (data.status == 'Failure') {
                                  alert(data.message);
                            }
                                
                        }, "json");
                       // window.location.href = 'index.php?m=sales&a=vw_quotation&c=_do_send_email_quotation&suppressHeaders=true&quotation_id='+ quotation_id +'&quotation_revision_id='+ quotation_revision_id;
                    }
            }
                        
            
           function onclick_button(button)
           {
               if(button.value=="Send")
                 {
                  }
               if(button.value=="")
                  {
                    }
                if(button.value=="")
                  {
                 }
           }
                                           
        function deleteRows(r)
        {
        var i=r.parentNode.parentNode.rowIndex;
        var a=new Array();
        a=document.getElementById("myTable").rows;
        document.getElementById("myTable").deleteRow(i);
        if(document.getElementById && a.length==8)
        {
        document.getElementById("file1").style.display=""; 
        document.getElementById("file2").style.display="none";
        }
        }
        
        function generate_print_quotation(quotation_id, quotation_revision_id) {
            if (quotation_id == 0 && quotation_revision_id == 0) { // viec gui mail thuc hien o ngoai danh sach cac quotation
                var quotation_str_id = get_quotationID_checked('check_list_quo', 'quotation_id');
                if (quotation_str_id != '') {
                    var quotation_split = quotation_str_id.split('&quotation_id[]=');
                    if ((parseInt(quotation_split.length) - 1) == 1) { // neu chon 1 quotation de in pdf, lay mac dinh quotation revision cuoi cung nhat de in
                        $.ajax({
                            type:"POST",
                            url:"?m=sales&a=vw_quotation&c=_do_generate_print&suppressHeaders=true", // trang cần xử lý (tức là controller là category có function là search)
                            data:"quotation_id="+quotation_id+"&quotation_revision_id="+quotation_revision_id, // Lấy dữ liệu của txt gửi đi
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
                                                print_quotation(quotation_id, quotation_revision_id, quotation_str_id);
                                            } else if (generate == 0) {
                                                print_quotation_html(quotation_id, quotation_revision_id, quotation_str_id);
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
                    alert('<?php echo $AppUI->_('Please select at least one quotation to print'); ?>');
            }
           } else {
                $.ajax({
                            type:"POST",
                            url:"?m=sales&a=vw_quotation&c=_do_generate_print&suppressHeaders=true", // trang cần xử lý (tức là controller là category có function là search)
                            data:"quotation_id="+quotation_id+"&quotation_revision_id="+quotation_revision_id, // Lấy dữ liệu của txt gửi đi
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
                                                print_quotation(quotation_id, quotation_revision_id, quotation_str_id);
                                            } else if (generate == 0) {
                                                print_quotation_html(quotation_id, quotation_revision_id, quotation_str_id);
                                            }
                                    }
                                    }
                                });
                                $('#div-popup').dialog('open');
                            }
                    });
           }
        }
        
        function print_quotation_html(quotation_id, quotation_revision_id, quotation_str_id) {
            if (quotation_id == 0 && quotation_revision_id == 0) { // viec gui mail thuc hien o ngoai danh sach cac quotation
                window.open('?m=sales&a=vw_quotation&c=_do_print_html&suppressHeaders=true'+ quotation_str_id, '_blank', '');
            } else { // gui mail thuc hien khi view 1 quotation revision
                            window.open('?m=sales&a=vw_quotation&c=_do_print_html&quotation_id='+quotation_id+'&quotation_rev_id='+quotation_revision_id+'&suppressHeaders=true', '_blank', '');
            }
            
        }
         
         function load_template(quotation_id, quotation_rev_id, status_rev) {
             $.ajax({
                type: "POST",
                url: "?m=sales&a=vw_quotation&c=_do_getlist_template&suppressHeaders=true",
                data: "status_rev="+status_rev+"&quotation_id="+quotation_id+"&quotation_rev_id="+quotation_rev_id,
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
                                                    var quotation_id = $('#quotation_id').val();
                                                    var quotation_rev_id = $('#quotation_rev_id').val();
                                                    if (checked == '') {
                                                        alert('please choose Template.');
                                                    } else {
                                                        load_add_template_items(quotation_id, quotation_rev_id, checked, status_rev);
                                                    }
                                                });
                                        }
                                    }
                                });
                            $('#div-popup').dialog('open');
                }
                
             });
         }
         
         function load_add_template_items(quotation_id, quotation_revision_id, template, status_rev) {
            var mang_giatri = $('#frm_all_quotation').serialize();
            mang_giatri = mang_giatri+'&quotation_id='+ quotation_id +'&quotation_revision_id='+ quotation_revision_id +'&status_rev='+ status_rev+'&template_id='+template;
            $.post('?m=sales&a=vw_quotation&c=_do_insert_template_quotation&suppressHeaders=true', mang_giatri,
                    function(data) {
                        if (data.status == 'Success') {
                            $('#div-popup').dialog('close');
                                load_quotation(data.quotation_id, data.quotation_revision_id, status_rev);
                        } else if (data.status == "Failure") {
                            alert(data.message);
                        }
                    }, "json");
         }
        function insert_term_condition(){
            $.ajax({
                type: "POST",
                url: "?m=sales&a=vw_tab_term&c=_load_term_condition&suppressHeaders=true",
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
                                                    var current_textarea = $('#quotation_revision_term_condition_contents').val()+"\n";
//                                                    alert(current_textarea);
                                                    var textAreaAttitude = $(this).val();
                                                        if (textAreaAttitude != '') {
                                                            $('#quotation_revision_term_condition_contents').val(current_textarea + textAreaAttitude);
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
                checked[i] = $(this).val()+'\n';
            });
        var textAreaAttitude = checked;
        return textAreaAttitude;
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
    
    function load_email(attention_id,act){
        if(act!=0){
            $('#email_'+act).html('');
            $('#email_'+act).load('?m=sales&a=vw_invoice&c=_get_email&suppressHeaders=true',{attention_id:attention_id});
            $('#sel_title_'+act).load('?m=sales&a=vw_invoice&c=get_title_contact&suppressHeaders=true',{attention_id:attention_id});
        }else{
            $('#email').html('');
            $('#email').load('?m=sales&a=vw_invoice&c=_get_email&suppressHeaders=true',{attention_id:attention_id});
            $('#sel_title').load('?m=sales&a=vw_invoice&c=get_title_contact&suppressHeaders=true',{attention_id:attention_id});
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
    
    function load_quotation_revision(quotation_no){
        quotation_no = quotation_no.trim();
        var quotation_rev = $('#quotation_revision').val();
        //alert(quotation_rev);
        $('#quo_rev').html('Loading...');
        $('#quo_rev').load('?m=sales&a=vw_quotation&c=load_quotationRev_get_quotationNo&suppressHeaders=true',{quotation_no:quotation_no,quotation_rev:quotation_rev});
    }

//    function checkUpdateStatus(){
//        var startus_id=$('#quotation_status_39').val();
//        if()
//    }
    function cancel_quotation(){
        $('#div_details').html('Loading...');
        $('#div_details').load('?m=sales&a=vw_quotation&suppressHeaders=true');
    }
    
    function load_total_quotation(item){
        var rev_tax = $('#quotation_revision_tax').val();
        var price = 0, quanlity= 0,discount=0, tax_dis = 0 ;
        var item_price = $('#quotation_item_price_'+item).val();
        var item_qty = $('#quotation_item_quantity_'+item).val();
        var item_dis = $('#quotation_item_discount_'+item).val();
        //var item_total = $('#hidden_quototal_item').val();
        var total = new Array();
        if(trim(item_price)!="")
        {
            price = item_price;
            price = price.replace(/,/g, "");
        }
        if(trim(item_qty)!="")
            quanlity = item_qty;
        if(trim(item_dis)!="")
            discount = item_dis;
        // Kiem tra tinh hop le cua gia tri truyen vao
        var check = true;
            if(isNaN(price) == true){
                alert('Please enter Price must is number');
                check = false;
                $('#quotation_item_price_'+item).val("");
                price = 0;
            }
            if(quanlity < 0 || isNaN(quanlity) == true){
                alert('Please enter Quatity must is number and  greater than 0');
                check = false;
                $('#quotation_item_quantity_'+item).val("");
                quanlity= 0;
                
            }
            if(isNaN(discount) == true || discount<0 || discount >100){
                alert('"Please enter Discount must is number and between 0 and 100"');
                check = false;
                $('#quotation_item_discount_'+item).val("");
                discount = 0;
            }
            var are_item = parseFloat(price) * parseInt(quanlity);
            if(trim(item_dis)!=""){
                tax_dis = parseFloat(are_item)*(parseFloat(discount)/100);
            }
            var item_amount = parseFloat(are_item) - parseFloat(tax_dis);
            document.getElementById('quo_amount_'+item).innerHTML = number_format(round_up(item_amount),2,'.',',');

            quo_load_total();
            quo_tax(rev_tax,0);
            var quo_due_total = document.getElementById('quo_total_item').innerHTML;
                quo_due_total = quo_remove_commas(quo_due_total);
            var quo_due_tax= $('#quo_revision_tax').val();
                quo_due_tax = quo_remove_commas(quo_due_tax);
            var quo_due = parseFloat(quo_due_total) + parseFloat(quo_due_tax);
            document.getElementById('quo_due').innerHTML = number_format(quo_due,2,'.',',');

    }
function quo_load_total(){
    var item_total=0;
    if(parseFloat($('#hidden_quototal_item').val())>0)
        item_total = $('#hidden_quototal_item').val();
        item_total = quo_remove_commas(item_total);
    var tmp = 0;
    for(var i=1;i<=quo_item_add_array.length;i++){
        tmp_arr[i-1]=quo_remove_commas(document.getElementById('quo_amount_'+quo_item_add_array[i-1]).innerHTML);
        tmp_arr[i-1] = round_up(parseFloat(tmp_arr[i-1]));
        tmp = parseFloat(tmp)+parseFloat(tmp_arr[i-1]);
        tmp = tmp.toFixed(2);
        
    }
    var total = parseFloat(tmp) +  parseFloat(item_total);
    document.getElementById('quo_total_item').innerHTML = number_format(total,2,'.',',');

}
function quo_tax(value,change){
    var total = $('#hidden_quototal_item').val();
    //alert(total);
    var total_quo = document.getElementById('quo_total_item').innerHTML;
    var discount = $('#quotation_revision_discount').val();
    total_quo = quo_remove_commas(total_quo);
    var total_quo_last_discount = parseFloat(total_quo) - parseFloat(discount);
    //alert(total_quo);
    var quo_inv_edit_value = "0.00";
    if(value!=0){
        var rate = $("#quotation_revision_tax :selected").text();
        //var total_tax = parseFloat(total_quo)*0.07;
        var total_tax = parseFloat(total_quo_last_discount)*parseFloat(rate/100);
        quo_inv_edit_value = number_format(round_up(total_tax),2,'.',',');
    }
    // kiem tra total co thay doi ko de thuc hien update tax
    if(parseFloat(total)!=parseFloat(total_quo) || change==1)
        $('#quo_revision_tax').val(quo_inv_edit_value);
    // End
    var quo_due_total = document.getElementById('quo_total_item').innerHTML;
    quo_due_total = quo_remove_commas(quo_due_total);
    var quo_due_tax= $('#quo_revision_tax').val();
    quo_due_tax = quo_remove_commas(quo_due_tax);
    var quo_due = parseFloat(total_quo_last_discount) + parseFloat(quo_due_tax);
    document.getElementById('quo_due').innerHTML = number_format(quo_due,2,'.',',');
    
        
}
function quo_remove_commas(str){
    str=str.toString();
    for(var i=0;i<str.length;i++){
        var str = str.replace(",","");                   
    }
    return str;
}

function enter_br(str){
        for(var i=0;i<str.length;i++)
            str = str.replace('\n','<br>');
        return str;
}
function un_enter_br(str){
        for(var i=0;i<str.length;i++)
            str = str.replace('<br>','\n');
        return str;
}
function _brank_load_quotation(){
    window.open('?m=sales&show_all=1&tab=0');
}
function _get_contact_jobLocation(jobLocation_id){
    var quotation_id = $('#quo_id').val();
    var customer_id = $('#customer_id').val();
    $('#contact_id').load('?m=sales&a=vw_quotation&c=_get_contact_jobLocation&suppressHeaders=true&job_location_id='+jobLocation_id);
    $('#address_contact').html('');
    $('#quo_contract_no').load('?m=sales&a=vw_quotation&c=load_contract_no&suppressHeaders=true',{customer_id:customer_id,quotation_id:quotation_id,job_location_id:jobLocation_id});
}
function addContactAddress() {
//  $('#address_contact_id').val(address_id);
//  popupAddnew('frm_add_brand_new_contact','Add Account');   
    var customer_id = $('#customer_id').val();
    var address_id = $('#job_location_id').val();
    if(customer_id==""||customer_id==undefined){
        alert("Please Provide client name.");
        return;
    }
    else if(address_id==""||address_id==undefined){
        alert("Please select Job Location.");
        return;
    }
    $.ajax({
        url:'?m=sales&a=frmNewContact&suppressHeaders=true',
        data: {client_id: customer_id, address_id: address_id , status: "quotation"},
        type: 'POST',
        success:function(data){
            $('#div-popup').html(data);
            popupAddnew('div-popup', 'Add Account');
        }
   });
                        
}
function load_email_contact(contact_id){
    $('#address_contact').load('?m=sales&a=vw_quotation&c=_get_email&suppressHeaders=true',{contact_id:contact_id});
}
function addContactAttention() {
    var customer_id = $('#customer_id').val();
    var address_id = $('#address_id').val();
    if(customer_id==""||customer_id==undefined){
        alert("Please Provide client name.");
        return;
    }
    else if(address_id==""||address_id==undefined){
        alert("Please select Address.");
        return;
    }
      var id = 'div-popup';
      $('#'+id).load('?m=sales&a=frmNewContact&suppressHeaders=true', { client_id: customer_id, address_id: address_id , status: "quotation", att_status:"attention" }).dialog({
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
    var quotation_id = $('#quo_id').val();
    var quotation_rev_id = $('#quo_rev_id').val();
    var countRev = $('#quo_countRev').val();
    if(value=="Change Revision"){
        change_revision(quotation_id,quotation_rev_id,"update");
    }
    else if(value == "History"){
        view_history(quotation_id,quotation_rev_id);
    }
    else if(value == "Delete"){
        delete_quotation_revision(quotation_id,quotation_rev_id,countRev);
    }
    else if(value == "Copy"){
        copy_quotation(quotation_id,quotation_rev_id);
    }
    else if(value == "Delete Quotation"){
        delete_one_quotation(quotation_id);
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
    var client_id = $('#customer_id').val();
    if(client_id==""||client_id==undefined){
        alert("Please Provide client name.");
        return;
    }
      var id = 'div-popup';
      $('#'+id).load('?m=sales&a=frmAddress&suppressHeaders=true', { client_id: client_id }).dialog({
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
function load_quotation_no_exist(){
    //alert("123");
    var dept_id = $('#department').val();
    $.post('?m=sales&a=vw_quotation&c=_do_load_quotation_no_exist&suppressHeaders=true',{acation:"exits",dept_id:dept_id},
    function(data){
        $('#quotation_no').val(data.quotation_no);
        $('#quotation_revision').val(data.quotation_rev);
        
    },"json");
}
function loadTaskEdit_quo(job_id,quotation_id,customer_id,job_location_id) {
        if (job_id == 0 && $('#sales_task_popup-editTask').html() != '' && $('#job_id').val() == 0) {
            
            popupEditTask('sales_task_popup-editTask', 'New Task');
        } else {
            $('#popup-editTask').html('');
        
            $.ajax({
                url:'?m=sales&a=frmNewTask&suppressHeaders=true',
                data: {job_id: job_id,customer_id:customer_id,job_location_id:job_location_id,quotation_id:quotation_id},
                type: 'POST',
                success:function(data){
                    $('#sales_task_popup-editTask').html(data);
                    var title = 'New Task';
                    if ($('#job_id').val() != 0)
                        title = 'Edit Task';
                    popupEditTask('sales_task_popup-editTask', title);
                }
            });
        }

}
function popupEditTask(id, message) {
        var id_='#'+id;

        $(id_).dialog({
                    resizable: false,
                    modal: true,
                    title: message,
                    width: 'auto',
                    close: function(ev,ui){$(id_).dialog('destroy');} 
            });
        $(id_).dialog('open');
}
function load_quo_cash2(){
    var table ='<table border="0" width="100%">\n\
        <tbody>\n\
            <tr valign="top">\n\
                <td width="19.5%" style="padding-top:5px;">\n\
                    Attention:<a class="icon-add icon-all" onclick="add_quo_inline_attention(); return false;" style="margin-right:0px;" href="#"></a>\n\
                    <input id="onchange_customer" type="hidden" value="1" name="onchange_customer">\n\
                </td>\n\
                <td width="62%">\n\
                    <span id="sel_title">\n\
                        <select id="contacts_title_id" class="text" size="1" style="height:20pt;" name="contacts_title_id[]"></select>\n\
                    </span>\n\
                    <span id="sel_attention">\n\
                        <select id="attention_id" class="text_select" onchange="load_email(this.value,0);" style="width:319px" name="attention_id[]"></select>\n\
                    </span>\n\
                    <div id="email" style="margin-top:5px;"></div>\n\
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
function add_quo_inline_attention(){
    var customer_id = $('#customer_id').val();
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
                        <select id="attention_id_'+count_attn+'" class="text_select" onchange="load_email(this.value,'+count_attn+');" style="width:319px" name="attention_id[]"></select>\n\
                    </span>\n\
                    <div style="margin-top:5px;" id="email_'+count_attn+'"></div>\n\
                </td>\n\
                <td>&nbsp;</td>\n\
            </tr>';
    $('#add_line_attn_foot').append(html);
    $('#sel_title_'+count_attn).load('?m=sales&a=vw_invoice&c=get_title_contact&suppressHeaders=true',{customer_id:customer_id,act_attn:1});
    $('#attention_id_'+count_attn).load('?m=sales&a=vw_invoice&c=_get_attention_select&suppressHeaders=true', { customer_id: customer_id,act_attn:1});
    $('#count_attn').val(parseInt(count_attn)+1);
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
function option_total_status(value){
    if(value==1){
        $('#div_4').css({'display':'block'});
    }
    else
        $('#div_4').css({'display':'none'});
}

// Create Contract By Quotation approved
        function create_contract_by_quotaiton_approved(quotation_id,status) {
            //var status = $('#quotation_status_id').val();
            if(status!=3)
            {
                alert('Quotation has to be approved first.');
                return false;
            }
            if (quotation_id != null) {
                window.open('?m=engagements&show_all=1&tab=0&active=add&quotation_id='+quotation_id,'_self')
            }
        }    
//Create Contract By Quotation approved - END 

function search_quotation_by_quotaiton_no(quotation_no,dept_id)
{
   //if(dept_id==undefined)
        dept_id = $('#department').val();
   if(quotation_no.length>1 || quotation_no.length==0)
        list_quotation('', '5', 'undefined', '', 'quotation_revision_id',dept_id,quotation_no);
}

function reload_form_button()
{
    var quotation_id = $('#quo_id').val();
    var quotation_revision_id = $('#quo_rev_id').val();
    var status_back = $('#quotation_status_id').val();
    var canEditDept = $('#can_edit_dept').val();
    $('#div_0').load('?m=sales&a=vw_quotation&c=reload_form_button&suppressHeaders=true',{quotation_id:quotation_id,quotation_revision_id:quotation_revision_id,status_back:status_back,canEditDept:canEditDept});
}

function viewContract(quotation_id)
{
      var id = 'div-popup';
      $('#'+id).load('?m=sales&a=frm_link_contract&suppressHeaders=true', { quotation_id: quotation_id }).dialog({
            resizable: false,
            modal: true,
            title: '<?php echo $AppUI->_('View contract'); ?>',
            width: 350,
            close: function(ev,ui){
                $('#'+id).dialog('destroy');
            }
            });
            $('#'+id).dialog('open');
}

function delete_one_quotation(quotation_id)
{
    var quotation_status_id = $('#quotation_status_id').val();
    if(quotation_status_id == 3){
        alert("You can not delete beacause this quotation was approved");
        return false;
    }
    if (confirm('<?php echo $AppUI->_('Are you sure to delete record(s)?'); ?>')) {
        $.post('?m=sales&a=vw_quotation&c=_do_remove_quotation&quotation_id[]='+ quotation_id +'&suppressHeaders=true', {  },
            function(data) {
                if (data.status == 'Success') {
                    back_quo_contracts(quotation_status_id);
                    $.post('?m=sales&a=vw_quotation&c=_do_update_done_sevice_order&quotation_id[]='+ quotation_id +'&suppressHeaders=true', {  },
                        function(data){

                        },"json");
                } else if (data.status == "Failure") {
                    alert(data.message);
                }
        }, "json");
    }
}

function search_quotation()
{
   var quotation_no = $('#search-quotation-no').val();
   var dept_id = $('#department').val();
   var status = $('#quotation_status_id').val();
   if(quotation_no.length>1 || quotation_no.length==0)
        list_quotation('', status , 'undefined', '', 'quotation_revision_id',dept_id,quotation_no);
   $('#stt-block').css({'display':'block'});
}

function load_discount()
{
    var rev_tax = $('#quotation_revision_tax').val();
    quo_tax(rev_tax,1);
}

function viewContractItem(status_id,quotation_id)
{
      var id = 'div-popup';
      $('#'+id).load('?m=sales&a=frm_link_contract&suppressHeaders=true', { quotation_id: quotation_id,status_id:status_id }).dialog({
            resizable: false,
            modal: true,
            title: '<?php echo $AppUI->_('View contract'); ?>',
            width: 350,
            close: function(ev,ui){
                $('#'+id).dialog('destroy');
            }
            });
            $('#'+id).dialog('open');
}
</script>
