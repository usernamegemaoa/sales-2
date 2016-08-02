<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}
//echo "<script src='js/chosen.jquery.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' type='text/css' href='style/default/chosen.css' />";
//
// Select2
echo "<script src='lib/select2/select2.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' type='text/css' href='lib/select2/select2.css' />";
require_once 'CInvoiceItem.php';
global $AppUI, $ocio_config;

$perms =& $AppUI->acl();
$canRead = $perms->checkModule( $m, 'view' );
$canEdit = $perms->checkModule( $m, 'edit' );
$canAdd = $perms->checkModule( $m, 'add' );
$canDelete = $perms->checkModule( $m, 'delete' );

$canView_report = $perms->checkModule( 'sales_report', 'view');
$canView_quotation = $perms->checkModule( 'sales_quotation', 'view');
$canView_invoice = $perms->checkModule( 'sales_invoice', 'view');
$canView_payment = $perms->checkModule('sales_payment', 'view');
$canView_creditNote = $perms->checkModule('sales_creditNote', 'view');
$canView_template = $perms->checkModule('sales_template', 'view');

if (!$canRead) {
        $AppUI->redirect( "m=public&a=access_denied" );
}

//insert total item
//            $q = new DBQuery();
//            $q->addTable('sales_invoice_item');
//            $q->addQuery('invoice_item_id,invoice_item_price,invoice_item_quantity,invoice_item_discount');
//
//            $rows = $q->loadList();
//            $total = 0;
//            
//            if (count($rows) > 0) {
//                foreach ($rows as $row) {
//                    $price=$row['invoice_item_price'];
//                    $quantity=$row['invoice_item_quantity'];
//                      $discount=      $row['invoice_item_discount'];
//                    $amount_item = (($price * $quantity) - ($price * $quantity * ($discount/100)));
//                    //insert vao db
//                    $invocieItem = new CInvoiceItem();
//                    $invocieItem->load($row['invoice_item_id']);
//                    $invocieItem->invoice_item_total=$amount_item;
//                    $invocieItem->store();
//                }
//            }
            
            
//    $AppUI->savePlace();
//    $titleBlock = new CTitleBlock( 'Sales', "sales-48.png", $m, "$m.$a" );
//    $titleBlock->show();
//
//    $AppUI->setstate( 'CompVwTab', $_GET['tab'] );
//    $tabBox = new CTabBoxJQ('?m=sales'.$task_id, '' , 0 );
//    $tabBox->setTabBoxID("tabs-view-" . $m);
//    $tabBox->setModuleName($m);
//    
//    $tabBox->add(DP_BASE_DIR . '/modules/sales/vw_quotation', 'Quotation');
//    $tabBox->add(DP_BASE_DIR . '/modules/sales/vw_invoice', 'Invoice');
//    //$tabBox->add(DP_BASE_DIR . '/modules/sales/vw_payment', 'Payment');
//    $tabBox->add(DP_BASE_DIR . '/modules/sales/vw_payment_new', 'Payment');
//    $tabBox->add(DP_BASE_DIR . '/modules/sales/vw_template', 'Templates');
//    $tabBox->add(DP_BASE_DIR . '/modules/sales/vw_report', 'Report');
//    
////    $tabBox->add('vw_tax', 'Tax');    
//    $tabBox->show();

$AppUI->savePlace();

$show_all = dPgetParam($_GET, 'show_all', 1);
if (!$show_all) $show_all = dPgetParam($_POST, 'show_all', 1);

if (isset($_GET['tab'])) {
    $AppUI->setState( 'SalesTab', $_GET['tab'] );
}
$tab_def = defVal( $AppUI->getState( 'SalesTab' ),  0 );

$obj = null;
if (!db_loadObject($sql, $obj)) {
    $AppUI->setMsg('Sales');
    $AppUI->setMsg('invalidID', UI_MSG_ERROR, true);
    $AppUI->redirect();
} else {
    $AppUI->savePlace();
}

$moddir = DP_BASE_DIR.'/modules/sales/';
$tabBox = new CTabBox( "?m=sales&show_all=1", '', $tab_def );
if ($show_all == 1) {
    if ($canRead)
        $tabBox->add( $moddir . 'view_quotation', 'Quotation' );
    
    if($canView_invoice)
        $tabBox->add( $moddir . 'view_invoice', 'Invoice' );
    if($canView_creditNote)
        $tabBox->add( $moddir.  'view_creditNote', 'Credit Note');
    
    if($canView_payment)
        $tabBox->add( $moddir . 'view_payment', 'Payment');
    
    if($canView_template)
        $tabBox->add( $moddir . 'view_template', 'Template');
    
    if($canView_report)
        $tabBox->add( $moddir . 'view_report', 'Report' );
    
    
    $tabBox->loadExtras($m);
    $tabBox->loadExtras($m, 'view');
    $tabBox->show();
}
?>
<div id="popup_sales"></div> 
<script type="text/javascript">
        $('.dataTables_length').css({'text-align':'left'});
        var id_poup = 'div-popup';
//        function load_quotation(){
//                $('#div_details').html('<?php echo $AppUI->_('Loading...');?>');
//		$('#div_details').load('?m=sales&a=quotation_details&suppressHeaders=true',function() {
//        	});
//        }
        $('#div_search').css({'overflow':'visible'});
        $('#main_app_div').css({'overflow':'visible'});
        $('.chzn-container').css({'top':'7'});
        
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
        
        function get_ID_checked(id_check, str_check) {
            var inputs = document.getElementsByName(id_check);
            var id = '';
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                    id += '&'+str_check+'[]='+inputs[i].value;
                }
            }
            return id;
        }
        function htmlchars(str){
            if(typeof(str) == "string"){
                str = str.replace(/&/g, "%26");
                str = str.replace(/"/g, "&quot;");
                str = str.replace(/'/g, "&#039;");
                str = str.replace(/<(?!br)/g, "&lt;");
                //str = str.replace(/>/g, "&gt;");
            }
            return str;
        }
        function is_int(value){
            if((parseFloat(value)==parseInt(value)) && !isNaN(value))
                return true;
            return false;
        }
        function no_special(str){
            //var re = /^([A-Za-z0-9\_\-]+\.)*[A-Za-z0-9\_\-]+@[A-Za-z0-9\_\-]+(\.[A-Za-z0-9\_\-]+)+$/;
            var re = /^([A-Za-z0-9\_\-\,\/\.]+)+$/;
            if(str.search(re)==-1)
                return false;
            return true;
        }
        function round_up(x){
            var r = x.toFixed(2);
            var tmp = parseFloat(x) - parseFloat(r);
            if(tmp>0.000001)
                return parseFloat(r) + 0.01;
            return r;
        }
        function number_format( number, decimals, dec_point, thousands_sep ) {
            // * example 1: number_format(1234.5678, 2, '.', '');
            // * returns 1: 1234.57
            var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
            var d = dec_point == undefined ? "," : dec_point;
            var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
            var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

            var number = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
            return number;
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
        function sales_done_serviceoder(id,quo_or_inv_id,type) {
           $.ajax({
              type: 'POST',
              url: '?m=serviceOrders&a=do_process_service_order_done&action=move_history&suppressHeaders=true',
              data: 'id='+id+'&field=service_oder_status_history',
              success: update_inv_quo_seviceOder(id,quo_or_inv_id,type)
           });
        }
        function update_inv_quo_seviceOder(id,quo_or_inv_id,type){
            if(type=="invoice"){
                //alert(quo_or_inv_id);
                $.ajax({
                   type: 'POST',
                   url: '?m=sales&a=vw_invoice&c=_do_inv_updat_sevice_order&suppressHeaders=true',
                   data: 'id_sevice='+id+'&invoice_id='+quo_or_inv_id
                });
            }else{
                $.ajax({
                   type: 'POST',
                   url: '?m=sales&a=vw_quotation&c=_do_quo_updat_sevice_order&suppressHeaders=true',
                   data: 'id_sevice='+id+'&quotation_id='+quo_or_inv_id
                });   
            }
        }
        function add_tax(){
            var id = 'div-popup';
            $('#'+id).load('?m=sales&a=vw_tax&c=popup_add_tax&suppressHeaders=true', {}).dialog({
                  resizable: false,
                  modal: true,
                  title: '<?php echo $AppUI->_('Add Tax'); ?>',
                  width: 350,
                  close: function(ev,ui){
                      $('#'+id).dialog('destroy');
                  }
                  });
                  $('#'+id).dialog('open');
        }
        function save_add_tax(){
                var tax_name = $('#tax_name').val();
                var tax_rate = $('#tax_value').val();
                if (isFloat(tax_rate)) {
                    $.post('?m=sales&a=configure&c=do_add_tax&suppressHeaders=true', { tax_name: tax_name, tax_rate: tax_rate },
                            function(data) {
                                if (data == 'success')  {
                                    closeDialogPopup("div-popup");
                                    $('#invoice_revision_tax').load('?m=sales&a=vw_tax&c=load_tax_option&suppressHeaders=true');
                                    $('#quotation_revision_tax').load('?m=sales&a=vw_tax&c=load_tax_option&suppressHeaders=true');
                                } else {
                                    closeDialogPopup("div-popup");
                                    $('#invoice_revision_tax').load('?m=sales&a=vw_tax&c=load_tax_option&suppressHeaders=true');
                                    $('#quotation_revision_tax').load('?m=sales&a=vw_tax&c=load_tax_option&suppressHeaders=true');
                                }
                            }
                    );
                } else {
                    alert('Tax rate should be float.');
                }
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
        function closeDialogPopup(id) {
                var id_='#'+id;
                $(id_).dialog('destroy');
        }
        function quo_remove_commas(str){
            str=str.toString();
            for(var i=0;i<str.length;i++){
                var str = str.replace(",","");                   
            }
            return str;
        }
        function lookupClient(inputString) {
//            var field_filter = $('#field_client_filter').val();
            if(inputString.length < 3) {
                // Hide the suggestion box.
                $('#client_suggestions').hide();
            } else {
                $('#img_loading').html('loading...');
                $.post(
                    "index.php?m=sales&a=vw_invoice&c=load_result_search_customer&suppressHeaders=true",
                    {company_search: inputString, field_filter: "address"},
                    function(data){
                        if(data.length >0) {
                            $('#img_loading').html('');
                            $('#client_suggestions').show();
                            $('#autoClientSuggestionsList').html(data);
                        }
                    }
                );
            }
        }
        function load_frm_search_customer(){
            var id = '#div-popup';
            $(id).load('?m=sales&a=frm_search_customer&suppressHeaders=true',{}).dialog({
                resizable: false,
                modal: true,
                title: 'Search Customer',
                width: 400,
                close: function(ev,ui){
                    $(id).dialog('destroy');
                }
            });
            $(id).dialog("open");
        }
        function ischeck_radio(id_check){
            var inputs = document.getElementsByName(id_check);
            for (var i = 0; i < inputs.length; i++){
                if (inputs[i].type == 'radio' && inputs[i].checked == true) {
                    return true;
                }
            }
            return false;
        }
    function save_template_item(item){
        var item_description = $('#item_'+item).html();
        var item_quantity = $('#quantity_'+item).html();
        var item_price = $('#price_'+item).html();
        var item_discount = $('#discount_'+item).html();
        var item_total = $('#total_'+item).html();
        var radio_val = $('#rad_item_template:checked').val();
        
        if(radio_val==0)
        {
          $.post('?m=sales&a=vw_quotation&c=_do_add_template_item&suppressHeaders=true',{item_description:item_description,item_quantity:item_quantity,item_price:item_price,item_discount:item_discount,item_total:item_total},
            function(data){
                 if(data.status=="success"){
                    alert("Template save success.");
                }
                if(data.status=="exits"){
                    alert("Exist template item.");
                }
            },"json");
        }
        else
        {
          var template_id = get_Template_checked('check_list_quo', 'template_id');
          if(template_id=="")
          {
              alert("Please choose Template.");
          }
          else
          {
                $.post('?m=sales&a=vw_quotation&c=_do_add_template_item'+template_id+'&suppressHeaders=true',{item_description:item_description,item_quantity:item_quantity,item_price:item_price,item_discount:item_discount,item_total:item_total,radio_val:radio_val},
                  function(data){
                       if(data.status=="success"){
                          alert("Template save success.");
                      }
                      if(data.status=="exits"){
                          alert("Exist template item.");
                      }
                  },"json");
          }
        }

    }
    function list_template_item(item,type) {
        $.ajax({
                    type: "POST",
                    url: "?m=sales&a=vw_template&c=_do_getlist_template_item&suppressHeaders=true",
                    data: {item:item,type:type},
                    success: function(x) {
                        $('#div-popup').html(x).dialog({
                                        resizable: true,
                                        modal: true,
                                        title: '<?php echo $AppUI->_('List Template Item'); ?>',
                                        width: 800,
                                        maxheight: 600,
                                        close: function(ev, ui) {
                                            $('#div-popup').dialog('destroy');
                                        },
                                    });
                                $('#div-popup').dialog('open');
                    }

                 });
    }
function load_apply_template_item(item,template_item_id,type){
    var template_item = $('#template_item_'+template_item_id).html();
        template_item = template_item.replace(/&amp;/g, '&');
        template_item = template_item.replace(/&nbsp;/g, ' ');
    var template_item_quantity = $('#item_temp_quan_'+template_item_id).html();
    var template_item_price = $('#item_temp_price_'+template_item_id).html();
    var template_item_discount = $('#item_temp_discount_'+template_item_id).html();
    for(var i=0; i<template_item_price.length;i++)
        template_item_price = template_item_price.replace(",","");
    if(type=="quotation"){
        $('#quotation_item_'+item).val(un_enter_br1(template_item));
        $('#quotation_item_quantity_'+item).val(template_item_quantity);
        $('#quotation_item_price_'+item).val(template_item_price);
        $('#quotation_item_discount_'+item).val(template_item_discount);
    }
    else{
        $('#invoice_item_'+item).val(un_enter_br1(template_item));
        $('#invoice_item_quantity_'+item).val(template_item_quantity);
        $('#invoice_item_price_'+item).val(template_item_price);
        $('#invoice_item_discount_'+item).val(template_item_discount);
    }

    $('#div-popup').dialog('destroy');
}
function un_enter_br1(str){
        for(var i=0;i<str.length;i++)
            str = str.replace('<br>','');
        return str;
}
function reverse_calculator(){
    var total = parseFloat($('#calculator_total').val());
    var tax = parseInt($('#calculator_tax').val());
    var total_tax =0;
    var total_sub = 0;
    var value_tax = 0;
    if(tax!="" && tax!=0)
        value_tax = parseFloat($('#calculator_tax option:selected').text());
    total_tax = total*(value_tax/100);
    total_sub = total - total_tax;
    
    var total_tax_format = number_format(total_tax,2,'.',',');
    var total_sub_format = number_format(total_sub,2,'.',',');
    
    $('#cal_tax_total').val(total_tax_format);
    $('#cal_sub_total').val(total_sub_format);
    
}
function load_rev_calculator(){
//    alert("!23");
    var id = 'div-popup';
    $('#'+id).load('?m=sales&a=frmRevCalculator&suppressHeaders=true',{}).dialog({
        resizable: false,
        modal: true,
        title: 'Reverse Calculator',
        width: 330,
        close: function(ev,ui){
            $('#'+id).dialog('destroy');
        }
    });
    $('#'+id).dialog('open');
}
function remove_template_item(item_id,item_applier,type){
         var item_temp_id = '&item_temp_id[]='+item_id;
        if (confirm('<?php echo $AppUI->_('Are you sure to delete record(s)?'); ?>')) {
                $.ajax({
                   type: "POST",
                   url: '?m=sales&a=vw_template&c=_do_remove_item'+ item_temp_id +'&suppressHeaders=true',
                   data: "template_id="+template_id,
                   success: function (data) {
                       $('#list_template_item').load('?m=sales&a=vw_template&c=tbl_list_template_item&suppressHeaders=true',{item:item_applier,type:type});
                   },
                   error: function() {
                       window.alert('fail.');
                   }
                });
}}
function get_Template_checked(id_check, str_check) {
        var inputs = document.getElementsByName(id_check);
        var template_id = '';
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                template_id += '&'+str_check+'[]='+inputs[i].value;
            }
        }
        return template_id;
}
 function popupFormSaveItemTemplate(item_id){
    var id = 'div-popup';

        $('#'+id).html("Loading ...");
        $('#'+id).load('?m=sales&a=vw_invoice&c=_form_save_item_template&suppressHeaders=true', { item_id: item_id }).dialog({
            resizable: false,
            modal: true,
            title: '<?php echo $AppUI->_('Save Item Template'); ?>',
            width: 400,
            maxheight: 400,
            close: function(ev,ui){
                $('#'+id).dialog('destroy');
            }
        });

        $('#'+id).dialog('open');
 }
 function load_list_template($value)
 {
     if($value==1)
        $('#form_list_template').load('?m=sales&a=vw_invoice&c=list_template&suppressHeaders=true');
     else
        $('#form_list_template').html('');
 }
 function save_template_subheading_or_subject(type,action)
 {
     var content = "";
     if(type=='subject')
        if(action=="quotation")
            content = $('#quotation_subject').val();
        else
            content = $('#invoice_subject').val();
    else
        content = $('#sub_heading').val();
    if(content=="")
    {
        return false;
    }
    else{
        $.ajax({
           type:'POST',
           url:'?m=sales&a=vw_invoice&c=save_template_subheading_or_subject&suppressHeaders=true',
           data:{type:type,content:content},
           success:function(data){
               var response = jQuery.parseJSON(data);
               if(response.status=="exist")
                   alert("Exist Template.");
               else if(response.status=="success")
                   alert("Template save success");
               else
                   alert('error');
           }
        });
    }
     
 }
 
function list_template_subject(type,action) {
    $.ajax({
                type: "POST",
                url: "?m=sales&a=vw_template&c=_do_getlist_template_subject&suppressHeaders=true",
                data: {type:type,action:action},
                success: function(x) {
                    $('#div-popup').html(x).dialog({
                                    resizable: true,
                                    modal: true,
                                    title: '<?php echo $AppUI->_('List Template Subject'); ?>',
                                    width: 500,
                                    maxheight: 600,
                                    close: function(ev, ui) {
                                        $('#div-popup').dialog('destroy');
                                    },
                                });
                            $('#div-popup').dialog('open');
                }

             });
}

function list_template_subheading(type) {
    $.ajax({
                type: "POST",
                url: "?m=sales&a=vw_template&c=_do_getlist_template_subheading&suppressHeaders=true",
                data: {type:type},
                success: function(x) {
                    $('#div-popup').html(x).dialog({
                                    resizable: true,
                                    modal: true,
                                    title: '<?php echo $AppUI->_('List Template Sub Heading'); ?>',
                                    width: 500,
                                    maxheight: 600,
                                    close: function(ev, ui) {
                                        $('#div-popup').dialog('destroy');
                                    },
                                });
                            $('#div-popup').dialog('open');
                }

             });
}

function load_apply_template_subject(template_item_id,type,action){
    var template_content = $('#template_item_'+template_item_id).html();
    template_content = template_content.replace(/<br>/g, '');
    template_content = template_content.replace(/&amp;/g, '&');
    template_content = template_content.replace(/&nbsp;/g, ' ');
    if(action=="quotation")
        $('#quotation_subject').val(template_content);
    else
        $('#invoice_subject').val(template_content);
    $('#div-popup').dialog('destroy');
}

function load_apply_template_subHeading(template_item_id,type){
    var template_content = $('#template_item_'+template_item_id).html();
    template_content = template_content.replace(/<br>/g, '');
    template_content = template_content.replace(/&amp;/g, '&');
    template_content = template_content.replace(/&nbsp;/g, ' ');
    $('#sub_heading').val(template_content);
    $('#div-popup').dialog('destroy');
}

function remove_template_subheading(template_subheading_id,type)
{
    if (confirm('<?php echo $AppUI->_('Are you sure to delete record(s)?'); ?>')) {
        $.ajax({
            type:'POST',
            url:'?m=sales&a=vw_template&c=_do_remove_template_subheading&suppressHeaders=true',
            data:{template_subheading_id:template_subheading_id},
            success:function(data){
                $('#list_template_subheading').load('?m=sales&a=vw_template&c=tbl_list_template_subheading&suppressHeaders=true',{type:type});
            }
        });
    }
}

function remove_template_subject(template_subject_id,type)
{
    if (confirm('<?php echo $AppUI->_('Are you sure to delete record(s)?'); ?>')) {
        $.ajax({
            type:'POST',
            url:'?m=sales&a=vw_template&c=_do_remove_template_subheading&suppressHeaders=true',
            data:{template_subheading_id:template_subject_id},
            success:function(data){
                $('#list_template_subject').load('?m=sales&a=vw_template&c=tbl_list_template_subject&suppressHeaders=true',{type:type});
            }
        });
    }
}
</script>