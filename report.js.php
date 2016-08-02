<script type="text/javascript">
//    $(documnet).ready(function(){
//        loadJQueryCalendar('payment_date_start', 'payment_date_start', 'dd/mm/yy', 'yy-mm-dd');
//        loadJQueryCalendar('payment_date_end', 'payment_date_end_hidden', 'dd/mm/yy', 'yy-mm-dd');
//        
//        
//    });
    $(document).ready(function () {
        loadJQueryCalendar('payment_date_start', 'payment_date_start_hidden', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('payment_date_end', 'payment_date_end_hidden', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('gst_from', 'hdd_gst_from', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('gst_to', 'hdd_gst_to', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('sales_from', 'hdd_sales_from', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('sales_to', 'hdd_sales_to', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('sta_from', 'hdd_sta_from', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('sta_to', 'hdd_sta_to', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('aging_date_from', 'hdd_aging_from', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('aging_date_to', 'hdd_aging_date_to', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('quo_sta_from_date', 'hdd_quo_sta_from_date', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('quo_sta_to_date', 'hdd_quo_sta_to_date', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('credit_from_date', 'hdd_credit_from_date', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('credit_to_date', 'hdd_credit_to_date', 'dd/M/yy', 'yy-mm-dd');
		 loadJQueryCalendar('cash_from', 'hdd_cash_from', 'dd/M/yy', 'yy-mm-dd');
        loadJQueryCalendar('cash_to','hdd_cash_to','dd/M/yy','yy-mm-dd');
        $('#title_head').add;
        $('#aging_customer').chosen();
        $('#aging_customer_chzn input').css({'height': '22px'});
        $('#aging_customer_chzn').css({'width': '320px'});
        $('#cash_customer').chosen();
        $('#cash_customer_chzn input').css({'height': '22px'});
        $('#journal_customer').chosen();
        $('#journal_customer_chzn input').css({'height': '22px'});
        $('#cash_customer_chzn').css({'top': '8px'});
        $('#journal_customer_chzn').css({'top': '8px', 'width': '400px'});
        $('#customer_selesct_gst').chosen();
        $('#customer_selesct_gst_chzn input').css({'height': '23px'});
        $('#customer_selesct_gst_chzn').css({'top': '8px', 'width': '340px'});
        $('#customer_selesct_sales').chosen();
        $('#customer_selesct_sales_chzn input').css({'height': '23px'});
        $('#customer_selesct_sales_chzn').css({'top': '8px', 'width': '340px'});
        $('#customer_statement').chosen();
        $('#customer_statement_chzn').css({'top': '8px'});
        $('#co_statement').chosen();
        $('#co_statement_chzn').css({'top': '8px'});
        $('#address_atatement').chosen();
        $('#address_atatement_chzn').css({'top': '8px', 'width': '455px'});
        $('#select_quo_statement').chosen();
        $('#select_quo_statement_chzn').css({'top': '8px', 'width': '500px'});
        $('#select_quo_statement_address').chosen();
        $('#select_quo_statement_address_chzn').css({'top': '8px', 'width': '500px'});
        $('#credit_customer').chosen();
        $('#credit_customer_chzn').css({'top': '8px', 'width': '300px'});
		$('#customer_selesct_cash').chosen();
        $('#customer_selesct_cash_chzn input').css({'height': '23px'});
        $('#customer_selesct_cash_chzn').css({'top': '8px', 'width': '340px'});
    });
    function genarate_print_aging_report(report) {
        var report_type = report;
        var date_start = $('#payment_date_start_hidden').val();
        var date_end = $('#payment_date_end_hidden').val();
        var date_aging_from = $('#hdd_from').val();
        var date_aging_to = $('#hdd_to').val();
        var customer_id = $('#aging_customer').val();
        var customer_id_arr = "";
        var data_from = $('#hdd_aging_from').val();
        var data_to = $('#hdd_aging_date_to').val();
        var group_by = $('#group_by_aging').val();
        var include_address = $('#include_address').val();
        if (report_type == 2) {
            var customer_id_cash = $('#cash_customer').val();
            if (customer_id_cash != null) {
                for (var i = 0; i < customer_id_cash.length; i++) {
                    customer_id_arr += '&customer_id_arr[]=' + customer_id_cash[i];
                }
            }
        }
        if (report_type == 3) {
            customer_id_arr = $('#journal_customer').val();
        }
        var group_by = $('#group_by_aging').val();
        var time_range = $('#time_range').val();
        if (group_by == 1)
            window.open('?m=sales&a=vw_report&c=view_tbl_aging&suppressHeaders=true&time_range=' + time_range + '&customer_id=' + customer_id + '&data_from=' + data_from + '&data_to=' + data_to + '&include_address=' + include_address+'&print=1', '_blank', '');
        else
            window.open('?m=sales&a=vw_report&c=view_table_brand_aging&suppressHeaders=true&time_range=' + time_range + '&customer_id=' + customer_id + '&data_from=' + data_from + '&data_to=' + data_to + '&include_address=' + include_address+'&print=1', '_blank', '');
       
//        $.ajax({
//            type: "POST",
//            url: "?m=sales&a=vw_report&c=_do_generate_print_aging_report&suppressHeaders=true", // trang cÃ¡ÂºÂ§n xÃ¡Â»Â­ lÃƒÂ½ (tÃ¡Â»Â©c lÃƒÂ  controller lÃƒÂ  category cÃƒÂ³ function lÃƒÂ  search)
//            success: function (x) {
//                $('#div-popup').html(x).dialog({
//                    resizable: true,
//                    modal: true,
//                    title: '<?php echo $AppUI->_('Generate'); ?>',
//                    width: 400,
//                    maxheight: 400,
//                    close: function (ev, ui) {
//                        $('#div-popup').dialog('destroy');
//                    },
//                    buttons: {
//                        'Cancel': function () {
//                            $(this).dialog('close');
//                        },
//                        'Print': function () {
//                            var generate = $('#generate').val();
//                            //                                alert(generate);
//                            if (generate == 1) {
//                                if (report_type == 1)
//                                    window.open('index.php?m=sales&a=vw_report&c=_do_print_aging_report&suppressHeaders=true&date_from=' + date_aging_from + '&date_to=' + date_aging_to + '&customer_id=' + customer_id + '&data_from=' + data_from + '&data_to=' + data_to + '&group_by=' + group_by + '&include_address=' + include_address, '_blank', '');
//                                else if (report_type == 2)
//                                    window.open('index.php?m=sales&a=vw_report&c=_do_print_cash_receipt&suppressHeaders=true&date_start=' + date_start + '&date_end=' + date_end + customer_id_arr, '_blank', '');
//                                else if (report_type == 3) {
//                                    window.open('index.php?m=sales&a=vw_report&c=_do_print_invoice_journal&suppressHeaders=true&date_start=' + date_start + '&date_end=' + date_end + '&customer_id_arr=' + customer_id_arr, '_blank', '');
//                                }
//                            } else if (generate == 0) {
//                                if (report_type == 1)
//                                    
//                                else if (report_type == 2)
//                                    window.open('?m=sales&a=vw_report&c=_do_print_html_cash_receipt&suppressHeaders=true&date_start=' + date_start + '&date_end=' + date_end + customer_id_arr, '_blank', '');
//                                else if (report_type == 3)
//                                    window.open('?m=sales&a=vw_report&c=_do_print_html_invoice_journal&suppressHeaders=true&date_start=' + date_start + '&date_end=' + date_end + '&customer_id_arr=' + customer_id_arr, '_blank', '');
//                            }
//                        }
//                    }
//                });
//                $('#div-popup').dialog('open');
//            }
//        });
    }
    
    function print_invoice_journal()
    {
        var date_start = $('#payment_date_start_hidden').val();
        var date_end = $('#payment_date_end_hidden').val();
        var customer_id_arr = $('#journal_customer').val();
        window.open('index.php?m=sales&a=vw_report&c=_do_print_invoice_journal&suppressHeaders=true&date_start=' + date_start + '&date_end=' + date_end + '&customer_id_arr=' + customer_id_arr, '_blank', '');
    }
    
    function print_html_invoice_journal()
    {
        var date_start = $('#payment_date_start_hidden').val();
        var date_end = $('#payment_date_end_hidden').val();
        var customer_id_arr = $('#journal_customer').val();
        window.open('index.php?m=sales&a=vw_report&c=_do_print_invoice_html_journal&suppressHeaders=true&date_start=' + date_start + '&date_end=' + date_end + '&customer_id_arr=' + customer_id_arr, '_blank', '');
    }
    
    function genarate_print_cash_report()
    {
        var date_start = $('#payment_date_start_hidden').val();
        var date_end = $('#payment_date_end_hidden').val();
        var customer_id_arr = "";
        var customer_id_cash = $('#cash_customer').val();
            if (customer_id_cash != null) {
                for (var i = 0; i < customer_id_cash.length; i++) {
                    customer_id_arr += '&customer_id_arr[]=' + customer_id_cash[i];
                }
        }
        window.open('index.php?m=sales&a=vw_report&c=_do_print_cash_receipt&suppressHeaders=true&date_start=' + date_start + '&date_end=' + date_end + customer_id_arr, '_blank', '');
    }
    function list_aging_report() {
        $('#aging_report').html('Loading...');
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_aging_report&suppressHeaders=true');
    }
    function list_cash_receipt() {
        $('#aging_report').html('Loading...');
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_cash_receipt&suppressHeaders=true');
    }
    function list_invoice_juornal() {
        $('#aging_report').html('Loading...');
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_invoice_journal&suppressHeaders=true');
    }
    
    function loadReport() {
        var date_start = $('#payment_date_start_hidden').val();
        var date_end = $('#payment_date_end_hidden').val();
        var customer_id_arr = "";
        var customer_id = $('#cash_customer').val();
        if (customer_id != null) {
            for (var i = 0; i < customer_id.length; i++) {
                customer_id_arr += '&customer_id_arr[]=' + customer_id[i];
            }
        }
        var date_between = checkDateFrom_To(date_start,date_end);

//        if(date_between < 370){
        $('#tbl_cash_receipt').html('Loading...');
        $('#tbl_cash_receipt').load('?m=sales&a=vw_report&c=view_tbl_cash_receipt&suppressHeaders=true&date_start=' + date_start + '&date_end=' + date_end + customer_id_arr);
//        }else{
//            alert('The distance between the Date From and Date To not allowed over 370 day');
//        }
    }
    function load_invoice_journal() {
        var date_start = $('#payment_date_start_hidden').val();
        var date_end = $('#payment_date_end_hidden').val();
        var custormer_id = $('#journal_customer').val();
        var date_between = checkDateFrom_To(date_start,date_end);

//        if(date_between < 370){
        $('#tbl_invoice_journal').html('Loading...');
        $('#tbl_invoice_journal').load('?m=sales&a=vw_report&c=view_tbl_invoice_journal&suppressHeaders=true&date_start=' + date_start + '&date_end=' + date_end + '&custormer_id=' + custormer_id);
//        }else{
//            alert('The distance between the Date From and Date To not allowed over 370 day');
//        }
    }
    function get_search_aging() {
        var time_range = $('#time_range').val();
        var customer_id = $('#aging_customer').val();
        var data_from = $('#hdd_aging_from').val();
        var data_to = $('#hdd_aging_date_to').val();
        var group_by = $('#group_by_aging').val();
        var include_address = $('#include_address').val();
        
        if ($('#check_live_report').is(':checked'))
        {
            if(check_live_report)
            //alert(include_address);
            $('#tbl_aging').html('Loading...');
            if (group_by == 1)
                $('#tbl_aging').load('?m=sales&a=vw_report&c=view_tbl_aging&suppressHeaders=true&time_range=' + time_range + '&customer_id=' + customer_id + '&data_from=' + data_from + '&data_to=' + data_to + '&include_address=' + include_address);
            else
                $('#tbl_aging').load('?m=sales&a=vw_report&c=view_table_brand_aging&suppressHeaders=true&time_range=' + time_range + '&customer_id=' + customer_id + '&data_from=' + data_from + '&data_to=' + data_to + '&include_address=' + include_address);
        }
        else
        {
            window.open("?m=sales&a=vw_report&c=_do_read_pdf&suppressHeaders=true&data_to="+data_to,'_blank','');
//        
        }
    }
    function list_gst_report() {
        $('#aging_report').html('Loading...');
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_gst_report&suppressHeaders=true');
    }
    function list_sales_report() {
        $('#aging_report').html('Loading...');
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_sales_report&suppressHeaders=true');
    }
    function get_search_gst() {
        var customer_id = $('#customer_selesct_gst').val();
        var from_date = $('#hdd_gst_from').val();
        var to_date = $('#hdd_gst_to').val();
        
        var date_between = checkDateFrom_To(from_date,to_date);

//        if(date_between < 370){
        $('#tbl_gst_report').html('Loading...');
        $('#tbl_gst_report').load('?m=sales&a=vw_report&c=view_gst_report&suppressHeaders=true', {customer_id: customer_id, from_date: from_date, to_date: to_date});
//        }else{
//            alert('The distance between the Date From and Date To not allowed over 370 day');
//        }
    }
    function print_gst_report() {
        var customer_id = $('#customer_selesct_gst').val();
        var customer_id_arr = "";
        if (customer_id != null) {
            for (var i = 0; i < customer_id.length; i++) {
                customer_id_arr += ',' + customer_id[i];
            }
        }
        var from_date = $('#hdd_gst_from').val();
        var to_date = $('#hdd_gst_to').val();
        $.ajax({
            type: "POST",
            url: "?m=sales&a=vw_report&c=_do_generate_print_report&suppressHeaders=true", // trang cáº§n xá»­ lÃ½ (tá»©c lÃ  controller lÃ  category cÃ³ function lÃ  search)

            success: function (x) {
                $('#div-popup').html(x).dialog({
                    resizable: true,
                    modal: true,
                    title: '<?php echo $AppUI->_('Generate'); ?>',
                    width: 400,
                    maxheight: 400,
                    close: function (ev, ui) {
                        $('#div-popup').dialog('destroy');
                    },
                    buttons: {
                        'Cancel': function () {
                            $(this).dialog('close');
                        },
                        'Print': function () {
                            var generate = $('#generate').val();
                            //                                alert(generate);
                            if (generate == 1) {
                                print_gst_report_sumamay(customer_id_arr, from_date, to_date, generate);
                            } else if (generate == 0) {
                                print_gst_report_sumamay(customer_id_arr, from_date, to_date, generate);
                            }
                        }
                    }
                });
                $('#div-popup').dialog('open');
            }
        });
//        var customer_id = $('#customer_selesct_gst').val();
//        var customer_id_arr ="";
//        if(customer_id != null){
//            for(var i=0;i<customer_id.length;i++){
//                customer_id_arr += ','+customer_id[i];
//            }
//        }
//        var from_date = $('#hdd_gst_from').val();
//        var to_date = $('#hdd_gst_to').val();
//        window.open('?m=sales&a=vw_report&c=_do_print_gst_report_pdf&suppressHeaders=true&customer_id_arr='+customer_id_arr+'&from_date='+from_date+'&to_date='+to_date,'_blank','');
    }

    function print_gst_report_sumamay(customer_id_arr, from_date, to_date, generate)
    {

        window.open('?m=sales&a=vw_report&c=_do_print_gst_report_pdf&suppressHeaders=true&customer_id_arr=' + customer_id_arr + '&from_date=' + from_date + '&to_date=' + to_date + '&generate=' + generate, '_blank', '');
    }
    function list_sales_report() {
        $('#aging_report').html('Loading...');
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_sales_report&suppressHeaders=true', function () {
            get_total_sales_report();
        });
    }
    function get_search_sales() {
        var customer_id = $('#customer_selesct_sales').val();
        var from_date = $('#hdd_sales_from').val();
        var to_date = $('#hdd_sales_to').val();
        var sel_report = $('#sel_report').val();
        var cash_total_total = 0;
        var cash_total_paid = 0;
        var cash_total_due = 0;
        var invoice_total_total = 0;
        var invoice_total_paid = 0;
        var invoice_total_due = 0;
        
        var date_between = checkDateFrom_To(from_date,to_date);

//        if(date_between < 370){
        if (sel_report == 0) {
            $('#tbl_sales_report').html('Loading...');
            $('#tbl_sales_report').load('?m=sales&a=vw_report&c=view_sales_report&suppressHeaders=true', {customer_id: customer_id, from_date: from_date, to_date: to_date},
            function () {
                $('#tbl_invoice_report').html('Loading...');
                $('#tbl_invoice_report').load('?m=sales&a=vw_report&c=view_invoice_report&suppressHeaders=true', {customer_id: customer_id, from_date: from_date, to_date: to_date},
                function () {
                    get_total_sales_report();
                });
            });
        } else if (sel_report == 1) {
            $('#tbl_sales_report').html('Loading...');
            $('#tbl_sales_report').load('?m=sales&a=vw_report&c=view_sales_report&suppressHeaders=true', {customer_id: customer_id, from_date: from_date, to_date: to_date},
            function () {
                get_total_sales_report();
            });
            $('#tbl_invoice_report').html('');
            $('#report_hr').html('');

        }
        else {
            $('#tbl_sales_report').html('');
            $('#tbl_invoice_report').html('Loading...');
            $('#tbl_invoice_report').load('?m=sales&a=vw_report&c=view_invoice_report&suppressHeaders=true', {customer_id: customer_id, from_date: from_date, to_date: to_date},
            function () {
                get_total_sales_report();
            });
            $('#report_hr').html('');
        }
//        }else{
//            alert('The distance between the Date From and Date To not allowed over 370 day');
//        }
    }
    function get_total_sales_report() {
        var cash_total_total = 0;
        var cash_total_paid = 0;
        var cash_total_due = 0;
        var cash_total_amount = 0;
        var invoice_total_total = 0;
        var invoice_total_paid = 0;
        var invoice_total_due = 0;
        var invoice_total_amount = 0;

        if ($('#cash_total_total').val() != undefined && $('#cash_total_paid').val() != undefined && $('#cash_total_due').val() != undefined) {
            cash_total_total = parseFloat($('#cash_total_total').val());
            cash_total_paid = parseFloat($('#cash_total_paid').val());
            cash_total_due = parseFloat($('#cash_total_due').val());
            cash_total_amount = parseFloat($('#cash_total_amount').val());

        }
        if ($('#invoice_total_total').val() != undefined && $('#invoice_total_paid').val() != undefined && $('#invoice_total_due').val() != undefined) {
            invoice_total_total = parseFloat($('#invoice_total_total').val());
            invoice_total_paid = parseFloat($('#invoice_total_paid').val());
            invoice_total_due = parseFloat($('#invoice_total_due').val());
            invoice_total_amount = parseFloat($('#invoice_total_amount').val());
        }
        var total = invoice_total_total + cash_total_total;
        var paid = invoice_total_paid + cash_total_paid;
        var due = invoice_total_due + cash_total_due;
        var amount = cash_total_amount + invoice_total_amount;

        var table = '<table width="100%" style="margin-top:10px;" border="0" cellpadding="4" cellspacing="0" id="aging_report" class="tbl">';
        table += '<tr style="font-weight:bold;background-color:#eee">';
        table += '<td style="background-color:#eee" width="54%" colspan="3">Sub Total</td>';
        table += '<td style="background-color:#eee" align="right" width="9%">$' + number_format(total, 2, '.', ',') + '</td>';
        table += '<td style="background-color:#eee"></td>';
        table += '<td style="background-color:#eee" align="right" width="9%">$' + number_format(paid, 2, '.', ',') + '</td>';
        table += '<td style="background-color:#eee" ></td>';
        
        table += '<td style="background-color:#eee" align="right" width="9%">$' + number_format(due, 2, '.', ',') + '</td>';
        table += '</tr>';
        table += '</table>';

        $('#sales_tbl_total').html(table);
    }
    function print_sales_report() {
        var customer_id = $('#customer_selesct_sales').val();
        var customer_id_arr = "";
        if (customer_id != null) {
            for (var i = 0; i < customer_id.length; i++) {
                customer_id_arr += ',' + customer_id[i];
            }
        }
        var from_date = $('#hdd_sales_from').val();
        var to_date = $('#hdd_sales_to').val();
        var report_type = $('#sel_report').val();
        window.open('?m=sales&a=vw_report&c=_do_print_sales_report_pdf&suppressHeaders=true&customer_id_arr=' + customer_id_arr + '&from_date=' + from_date + '&to_date=' + to_date + '&report_type=' + report_type, '_blank', '');
    }
    function list_dpInfo_report() {
        $('#aging_report').html('Loading...');
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_dpInfo_report&suppressHeaders=true');
    }
    function load_export_excel() {
        window.open('?m=sales&a=exportExcel&suppressHeaders=true', '_self');
    }
//    function filter_sales_report(item){
//        if(item==1)
//            $('#tbl_invoice_report').html('');
//        else
//            $('#tbl_sales_report').html('');
//    }
    function list_statement_report() {
        $('#aging_report').html('Loading...');
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_statements_report&suppressHeaders=true');
    }
    function load_tabl_customer_statement(customer_id, action) {
        $('#filter_sta').load('?m=sales&a=vw_report&c=vw_filter_sta&suppressHeaders=true');
        $('#sel_address').load('?m=sales&a=vw_report&c=get_address_by_customer&suppressHeaders=true', {customer_id: customer_id});
        $('#attention_atatement').load('?m=sales&a=vw_report&c=get_attention_by_customer&suppressHeaders=true', {customer_id: customer_id});
    }
    function print_statement_report(customer_id) {
        var customer_id = $('#customer_statement').val();
        var load_inv = $('#load_invoice').val();
        var sta_date_from = $('#hdd_sta_from').val();
        var sta_date_to = $('#hdd_sta_to').val();
        var address_id = $('#address_atatement').val();
        var attention_val = $('#attention_atatement').val();
        var text_address = $('#text_address').html();
        //alert(text_address);
        var attention_state = "";
        if (attention_val != "")
            attention_state = $('#attention_atatement :selected').text();
        var co_stament_id = $('#co_statement').val();
        window.open('?m=sales&a=vw_report&c=_do_print_statement_report_pdf&suppressHeaders=true&customer_id=' + customer_id + '&load_inv=' + load_inv + '&sta_date_from=' + sta_date_from + '&sta_date_to=' + sta_date_to + '&address_id=' + address_id + '&attention_state=' + attention_state + '&text_address=' + text_address + '&co_stament_id=' + co_stament_id, '_blank', '');
    }
    function search_statement_report() {
        var customer_id = $('#customer_statement').val();
        var load_inv = $('#load_invoice').val();
        var sta_date_from = $('#hdd_sta_from').val();
        var sta_date_to = $('#hdd_sta_to').val();
        var address_id = $('#address_atatement').val();
        var attention_state = "";
        var attention_val = $('#attention_atatement').val();
        if (attention_val != "")
            attention_state = $('#attention_atatement :selected').text();
        var co_stament = "";
        var co_stament_id = $('#co_statement').val();
        if (co_stament_id != "")
            co_stament = $('#co_statement :selected').text();
        if (address_id == "-1") {
            alert("Please Choose Address.");
            return false;
        }
        var date_between = checkDateFrom_To(sta_date_from,sta_date_to);

//        if(date_between < 370){
        $('#tbl_statements').html('Loading...');
        $('#tbl_statements').load('?m=sales&a=vw_report&c=view_tbl_statements&suppressHeaders=true', {customer_id: customer_id, load_inv: load_inv, sta_date_from: sta_date_from, sta_date_to: sta_date_to, address_id: address_id, attention: attention_state, co_stament: co_stament});
//        }else{
//            alert('The distance between the Date From and Date To not allowed over 370 day');
//        }
    }
    function load_tabl_address_statement(address_id) {
        var attention_state = "";
        var co_stament = "";
        var attention_val = $('#attention_atatement').val();
        var customer_id = $('#customer_statement').val();
        var co_stament_id = $('#co_statement').val();
        if (attention_val != "")
            attention_state = $('#attention_atatement :selected').text();
        $('#filter_sta').load('?m=sales&a=vw_report&c=vw_filter_sta&suppressHeaders=true');
        $('#tbl_statements').html('Loading...');
        $('#tbl_statements').load('?m=sales&a=vw_report&c=view_tbl_statements&suppressHeaders=true', {customer_id: customer_id, address_id: address_id, attention: attention_state});
    }
    function list_quo_statement_report() {
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_quotation_statement&suppressHeaders=true', {});
    }
    function load_search_quo_statement() {
        var group_by = $('#quotation_gruop').val();
        var customer_id = $('#select_quo_statement').val();
        var address_id = $('#select_quo_statement_address').val();
        var status_id = $('#quotation_status_id').val();
        var from = $('#hdd_quo_sta_from_date').val();
        var to = $('#hdd_quo_sta_to_date').val();
//        alert(status_id);
       var date_between = checkDateFrom_To(from,to);

//        if(date_between < 370){
            if (group_by == 1)
                $('#tbl_quotation_statement').load('?m=sales&a=vw_report&c=view_tbl_quotation_statement&suppressHeaders=true', {customer_id: customer_id, status_id: status_id, address_id: address_id, from: from, to: to});
            else
                $('#tbl_quotation_statement').load('?m=sales&a=vw_report&c=view_tbl_quotation_statement_brand&suppressHeaders=true', {customer_id: customer_id, status_id: status_id, address_id: address_id, from: from, to: to});
//        }else{
//            alert('The distance between the Date From and Date To not allowed over 370 day');
//        }
        //$('#aging_report').load('?m=sales&a=vw_report&c=view_tbl_quotation_statement_brand&suppressHeaders=true',{});
    }
    function load_address_quo_sta_by_customer(customer_id) {
        $('#sel_quo_sta_address').load('?m=sales&a=vw_report&c=get_address_quo_by_customer&suppressHeaders=true', {customer_id: customer_id});
    }
    function load_print_quo_statement() {
        var customer_id = $('#select_quo_statement').val();
        var address_id = $('#select_quo_statement_address').val();
        var status_id = $('#quotation_status_id').val();
        var from = $('#hdd_quo_sta_from_date').val();
        var to = $('#hdd_quo_sta_to_date').val();
        var quotation_gruop = $('#quotation_gruop').val();
        if (quotation_gruop == 1)
            window.open('?m=sales&a=vw_report&c=_do_print_quo_statement&suppressHeaders=true&customer_id=' + customer_id + '&status_id=' + status_id + '&address_id=' + address_id + '&from=' + from + '&to=' + to, '_blank');
        else
            window.open('?m=sales&a=vw_report&c=_do_print_quo_statement_brand&suppressHeaders=true&customer_id=' + customer_id + '&status_id=' + status_id + '&address_id=' + address_id + '&from=' + from + '&to=' + to, '_blank');
    }

    function list_credit_note()
    {
        $('#aging_report').load('?m=sales&a=vw_report&c=view_credit_note&suppressHeaders=true');
    }

    function load_data_credit_note()
    {
        var customer_id = $('#credit_customer').val();
        var from_date = $('#hdd_credit_from_date').val();
        var to_date = $('#hdd_credit_to_date').val();
        var invoice_status_id = $('#invoice_status_id').val();
        
        var date_between = checkDateFrom_To(from_date,to_date);

//        if(date_between < 370){
        $('#tbl_credit_note').load('?m=sales&a=vw_report&c=tbl_credit_note&suppressHeaders=true', {customer_id: customer_id, from_date: from_date, to_date: to_date,invoice_status_id:invoice_status_id});
//         }else{
//            alert('The distance between the Date From and Date To not allowed over 370 day');
//        }
    }

    function load_print_credit_note()
    {
        var customer_id = $('#credit_customer').val();
        var from_date = $('#hdd_credit_from_date').val();
        var to_date = $('#hdd_credit_to_date').val();
        var invoice_status_id = $('#invoice_status_id').val();
        window.open('?m=sales&a=vw_report&c=_do_print_credit_note&suppressHeaders=true&customer_id=' + customer_id + '&from_date=' + from_date + '&to_date=' + to_date +'&invoice_status_id='+invoice_status_id, '_blank');

    }

    function list_profit()
    {
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_statements_profit&suppressHeaders=true');
    }
    function search_statements_profit() {

        var sta_date_from = $('#hdd_sta_from').val();
        var sta_date_to = $('#hdd_sta_to').val();
        var date_between = checkDateFrom_To(sta_date_from,sta_date_to);
//        if(date_between < 370){
        $('#tbl_profit').load('?m=sales&a=vw_report&c=view_profit&suppressHeaders=true', {from_date: sta_date_from, to_date: sta_date_to});
//        }else{
//            alert('The distance between the Date From and Date To not allowed over 370 day');
//        }
    }

    function print_profit_report() {
        var sta_date_from = $('#hdd_sta_from').val();
        var sta_date_to = $('#hdd_sta_to').val();
        alert(sta_date_from);
//        window.open('?m=sales&a=vw_report&c=_do_print_statement_report_pdf&suppressHeaders=true&customer_id='+customer_id+'&load_inv='+load_inv+'&sta_date_from='+sta_date_from+'&sta_date_to='+sta_date_to+'&address_id='+address_id+'&attention_state='+attention_state+'&text_address='+text_address+'&co_stament_id='+co_stament_id,'_blank','');
    }
    function checkDateFrom_To(date_from,date_to){
        var ds = new Date(date_from);
        var de = new Date(date_to);
        var date_start = ds.getTime();
        var date_end = de.getTime();
        var date_between = (date_end - date_start)/(86400*1000);
        return date_between;
	}
    function list_cash_flow_summary() {
        $('#aging_report').html('Loading...');
        $('#aging_report').load('?m=sales&a=vw_report&c=vw_cash_flow_summary&suppressHeaders=true');
    }
    function get_search_cash() {
        var customer_id = $('#customer_selesct_cash').val();
        var from_date = $('#hdd_cash_from').val();
        var to_date = $('#hdd_cash_to').val();
        $('#tbl_cash_report').html('Loading...');
     //   $('#tbl_cash_report').load('?m=sales&a=vw_report&c=view_cash_flow_summary_report&suppressHeaders=true&customer_id='+ customer_id + '&from_date=' + from_date + '&to_date=' + to_date);
        $('#tbl_cash_report').load('?m=sales&a=vw_report&c=view_cash_flow_summary_report&suppressHeaders=true', {customer_id: customer_id, from_date: from_date, to_date: to_date});
    }
    function print_cash_flow_summary(){
        var customer_id = $('#customer_selesct_cash').val();
        var from_date = $('#hdd_cash_from').val();
        var to_date = $('#hdd_cash_to').val();
        window.open('?m=sales&a=vw_report&c=view_cash_flow_summary_report&suppressHeaders=true&customer_id_arr=' + customer_id + '&from_date=' + from_date + '&to_date=' + to_date, '_blank', '');
    }
</script>
