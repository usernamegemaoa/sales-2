<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $AppUI;

?>
<script type="text/javascript">
    $(document).ready(function() {
            var tr = '<tfoot id="add-inline-foot" name="add-inline-form" method="POST">';
            tr += '<input type="hidden" id="count_row_item" value="1">';
            tr += '</tfoot>';
            $('#detail_quotation_table').append(tr);
//            $('#template_item_table').dataTable({
//                        "bSort" : false,
//                        "bRetrieve":true,
//                        "bDestroy": true,
//                     });
     });
     
    function new_template() {
           $.ajax({
                type: "POST",
                url: "?m=sales&a=vw_template&c=_load_add_template&suppressHeaders=true",
                data: "",
                success: function (x) {
                    $('#div-popup').html(x).dialog({
                                    resizable: true,
                                    modal: true,
                                    title: '<?php echo $AppUI->_('New Template'); ?>',
                                    width: 400,
                                    maxheight: 400,
                                    close: function(ev, ui) {
                                        $('#div-popup').dialog('destroy');
                                    },
                                    buttons: {
                                    'Cancel': function() {
                                        $(this).dialog('close');
                                    },
                                    'Save': function() {
                                            var template_name = $('#templ_name').val();
                                            template_name = template_name.replace(/&/g, "%26");
                                            var template_type = $('#template_type').val();
                                            if (template_name != '') {
                                            $.ajax({
                                                     type: "POST",
                                                     data: "template_name="+template_name+"&template_type="+template_type,
                                                     url: "?m=sales&a=vw_template&c=_do_add_template&suppressHeaders=true",
//                                                     dataType: "json",
                                                     success: function(html){
//                                                            var template_id = html.templ_id;
//                                                            alert(template_id);
                                                                $('#div-popup').dialog('close');
//                                                                load_template_theme(html.templ_id);
                                                                back_list_template();
                                                         
                                                     },
                                                     error: function() {
                                                         window.alert('not insert.')
                                                     }
                                                  });
                                            } else {
                                                alert('Please enter name Template.');
                                            }
                                        }
                                    }
                            });
                            $('#div-popup').dialog('open');
                }
           });
    }

    
    function load_template_theme(template_id) {
            $('#tempalte').html('Loading...');
            $('#tempalte').load('?m=sales&a=vw_template&c=_view_detail_template&suppressHeaders=true', {templ_id: template_id});
//            loadTab_item(template_id);
    }
    function loadTab_item(template_id){
             $('#tab_detail').html('Loading...');
             $('#tab_detail').load('?m=sales&a=vw_tab_item&suppressHeaders=true', {template_id: template_id});
   }
   function loadtab_note(template_id){
        $('#tab_detail').html('Loading...');
        $('#tab_detail').load('?m=sales&a=vw_tab_note&suppressHeaders=true', {template_id: template_id});
   }
    function loadtab_term(template_id){
        $('#tab_detail').html('Loading...');
        $('#tab_detail').load('?m=sales&a=vw_tab_term&suppressHeaders=true', {template_id: template_id});
    }

    function back_list_template() {
        $('#tempalte').html('Loading...');
        $('#tempalte').load('?m=sales&a=vw_template&c=list_template&suppressHeaders=true');
    }
    
  function add_template_item(template_id) {

            var count_row = $('#count_row_item').val();
            var tr = '';
            tr += '<tr id="row_item_'+ count_row +'">';
            tr += '<td>';
            tr += '<button name="save" id="save" value="Save" onclick="save_inline();" class="btn">Save</button>'
            tr += '</td>';
            tr += '<td>';
            tr += '</td>';
            tr += '<td width="500">';
            tr += '<textarea cols="60" style="height:60px" rows="5 type="text" class="text" id="templ_item_name" name="templ_item_name[]"/></textarea>';
            tr += '</td>';
            tr += '<td align="right">';
            tr += '<input type="text" class="text" id="templ_item_quan" name="templ_item_quan[]" size="8"/>';
            tr += '</td>';
            tr += '<td align="right">';
            tr += '<input type="text" class="text" id="templ_item_price" name="templ_item_price[]" size="8"/>';
            tr += '</td>';
            tr += '<td align="right">';
            tr += '<input type="text" class="text" id="templ_item_discount" name="templ_item_discount[]" size="8"/>';
            tr += '</td>';
            tr += '<td>';
            tr += '<img border="0" style="cursor: pointer" onclick="remove_item_inline('+ count_row +');" src="images/icons/stock_delete-16.png">';
            tr += '<input type="hidden" class="text" id="quotation_item_id" name="quotation_item_id[]" value="0"/>';
            tr += '<input type="hidden" class="text" id="template_id" name="template_id" value="'+template_id+'"/>';
            tr += '</td>';
            tr += '</tr>';

            $('#add-inline-foot').append(tr);
            $('#count_row_item').val(parseInt(count_row) + 1);

        }
  function add_term_condition() {

            $.ajax({
                type: "POST",
                url: "?m=sales&a=vw_template&c=_load_add_term_condition&suppressHeaders=true",
                data: "",
                success: function (x) {
                    $('#div-popup').html(x).dialog({
                                    resizable: true,
                                    modal: true,
                                    title: '<?php echo $AppUI->_('New Term And Condition'); ?>',
                                    width: 520,
                                    maxheight: 400,
                                    close: function(ev, ui) {
                                        $('#div-popup').dialog('destroy');
                                    },
                                    buttons: {
                                    'Cancel': function() {
                                        $(this).dialog('close');
                                    },
                                    'Save': function() {
                                            var term_default = 0;
                                            if($("#term_default").is(':checked')){
                                                term_default = 1;
                                            }
                                            var term_and_condition = $('#term_and_condition').val();
                                            term_and_condition = term_and_condition.replace(/&/g, "%26");
                                            var template_type = $('#template_type').val();
                                            if (term_and_condition != '') {
                                            $.ajax({
                                                     type: "POST",
                                                     data: "term_and_condition="+term_and_condition+"&template_type="+template_type+"&term_default="+term_default,
                                                     url: "?m=sales&a=vw_template&c=add_item_term_condition&suppressHeaders=true",
//                                                     dataType: "json",
                                                     success: function(html){
//                                                            var template_id = html.templ_id;
//                                                            alert(template_id);
                                                                $('#div-popup').dialog('close');
//                                                                load_template_theme(html.templ_id);
                                                                term_condition_manager();
 
                                                         
                                                     },
                                                     error: function() {
                                                         window.alert('Erreur de transmission au serveur.')
                                                     }
                                                  });
                                            } else {
                                                alert('Please enter item name.');
                                            }
                                        }
                                    }
                            });
                            $('#div-popup').dialog('open');
                }
           });

        }
        function save_inline_term() {
                term_conttent = $('#term_conttent').val();
                if(term_conttent != '') {
                $.ajax({
                    type: "POST",
                    data: "term_conttent="+term_conttent,
                    url: "?m=sales&a=vw_template&c=add_item_term_condition&suppressHeaders=true",
                    success: function(data) {
                            term_condition_manager();
                    },
                    error: function () {
                        window.alert('Erreur de transmission au serveur');
                    }
                });
                } else {
                    alert('Please enter item name');
                }
        }
        function save_inline() {
                template_id = $('#template_id').val();
                name = $('#templ_item_name').val();
                quantity = $('#templ_item_quan').val();
                price = $('#templ_item_price').val();
                discount = $('#templ_item_discount').val();
                if(name != '') {
                $.ajax({
                    type: "POST",
                    data: "template_id="+template_id+"&templ_item_name="+name+"&templ_item_quan="+quantity+"&templ_item_price="+price+"&templ_item_discount="+discount,
                    url: "?m=sales&a=vw_template&c=add_item&suppressHeaders=true",
                    success: function(data) {
                            loadTab_item(template_id);
                    },
                    error: function () {
                        window.alert('Erreur de transmission au serveur');
                    }
                });
                } else {
                    alert('Please enter item name');
                }
        }
    function delete_template(template) {
        var template_id = get_Template_checked('check_list_quo', 'template_id');
            if (template_id != '') {
                if (confirm('<?php echo $AppUI->_('Are you sure to delete record(s)?'); ?>')) {
                    $.ajax({
                       type: "POST",
                       url: '?m=sales&a=vw_template&c=_do_remove_template'+ template_id +'&suppressHeaders=true',
                       data: "",
                       success: function (data) {
                           back_list_template();
                       },
                       error: function() {
                           window.alert('fail.');
                       }
                    });
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one Template to delete'); ?>');
            }
    }
    
   function delete_items_item(template_id) {
       var item_temp_id = get_Template_checked('item_check_list', 'item_temp_id');
       alert(item_temp_id);
            if (item_temp_id != '') {
                if (confirm('<?php echo $AppUI->_('Are you sure to delete record(s)?'); ?>')) {
                    $.ajax({
                       type: "POST",
                       url: '?m=sales&a=vw_template&c=_do_remove_item'+ item_temp_id +'&suppressHeaders=true',
                       data: "template_id="+template_id,
                       success: function (data) {
                           loadTab_item(template_id);
                       },
                       error: function() {
                           window.alert('fail.');
                       }
                    });
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one item to delete'); ?>');
            }
   }
   function delete_items_term(term_id) {
       var item_temp_id = get_Template_checked('item_check_list', 'item_temp_id');
            if (item_temp_id != '') {
                if (confirm('<?php echo $AppUI->_('Are you sure to delete record(s)?'); ?>')) {
                    $.ajax({
                       type: "POST",
                       url: '?m=sales&a=vw_template&c=_do_remove_term'+ item_temp_id +'&suppressHeaders=true',
                       data: "term_id="+term_id,
                       success: function (data) {
                            term_condition_manager();
                       },
                       error: function() {
                           window.alert('fail.');
                       }
                    });
                }
            } else {
                alert('<?php echo $AppUI->_('Please select at least one item to delete'); ?>');
            }
   }
   function save_notes(template_id, note_id) {
        var notes = $('#template_note').val();
        notes = notes.replace(/&/g,'%26');
        $.ajax({
                 type: "POST",
                 url: '?m=sales&a=vw_template&c=_do_add_notes&suppressHeaders=true',
                 data: "template_id="+template_id+"&notes="+notes+"&note_id="+note_id,
                 success: function (data) {
                    loadtab_note(template_id);
                 },
                 error: function() {
                    window.alert('fail.');
                 }
         });
   }
   function save_term_condition(template_id, term_id) {
        var term_condition = $('#term_condition').val();
        term_condition = term_condition.replace(/&/g,'%26');
        $.ajax({
                 type: "POST",
                 url: '?m=sales&a=vw_template&c=_do_add_term_condition&suppressHeaders=true',
                 data: "template_id="+template_id+"&term_condition="+term_condition+"&term_id="+term_id,
                 success: function (data) {
                    loadtab_term(template_id);
                 },
                 error: function() {
                    window.alert('fail.');
                 }
         });
   }
   function edit_inline_item(item_id) {
        var aaa = $('#item_'+item_id).val(); // lay ra item
        var template_id = $('#template_id').val();
            var bbb = aaa.replace(/"/g, "&quot;") // replace item sang ky tu dac biet
                    var tr = '';
//                    tr += '<form id="edit_items_form" method="POST" name="edit_items_form">';
                    tr += '<td>';
                    tr += '<a href="#" onclick="loadTab_item('+template_id+'); return false;">Cancel</a>';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<a href="#" onclick="save_inline_edit('+item_id+'); return false;">save</a>';
                    tr += '<input type="hidden" class="text" id="stt_'+item_id+'" name="" value="'+ $('#stt_'+item_id).html() +'"/>';
                    tr += '</td>';
                    tr += '<td width="500">';
                    tr += '<textarea rows="5" cols="80" class="text" style="height:55px;" id="item_'+item_id+'" name="item_temp_item[]">'+ bbb +'</textarea>';
                    tr += '</td>';
                    tr += '<td align="right">';
                    tr += '<input type="text" class="text" id="quantity_'+item_id+'" name="item_temp_quan[]" value="'+ $('#quantity_'+item_id).html() +'" size="8"/>';
                    tr += '</td>';
                    tr += '<td align="right">';
                    tr += '<input type="text" class="text" id="price_'+item_id+'" name="item_temp_price[]" value="'+ $('#price_'+item_id).html() +'" size="8"/>';
                    tr += '</td>';
                    tr += '<td align="right">';
                    tr += '<input type="text" class="text" id="discount_'+item_id+'" name="item_temp_discount[]" value="'+ $('#discount_'+item_id).html() +'" size="8"/>';
                    tr += '<input type="hidden" class="text" id="item_temp_id" name="item_temp_id[]" value="'+item_id+'"/>';
                    tr += '</td>';
                    tr += '<td>';
                    tr += '<input type="hidden" class="text" id="total_'+item_id+'" name="" value="'+ $('#total_'+item_id).html() +'"/>';
                    tr += '</td>';
//                    tr += '</form>';
                    $('#row_item_'+item_id).html(tr);
   }
   function edit_inline_term() {
       var term_condition_id = get_Template_checked('item_check_list', 'term_condition_id');
       var term_condition_split = term_condition_id.split('&term_condition_id[]=');
       if(term_condition_id == ''){
           alert('Please choose Term And Condition.');
       } else if((parseInt(term_condition_split.length)-1)>1)
           alert('Sorry the system allows choose Term And Condition.');
       else {
           save_inline_edit_term(term_condition_id);
       }
   }
   function hide_inline_item(item_id) {
                    var tr = '';
                    tr += '<td align="center">';
                    tr += '<input type="checkbox" value="'+item_id+'" name="item_check_list" id="item_check_list">';
                    tr += '<a href="#" class="icon-edit icon-all" onclick="edit_inline_item('+item_id+'); return false;" href="#" title="Inline Edit" style="margin-right:0px"></a>';
                    tr += '</td>';
                    tr += '<td align="center" id="stt_'+item_id+'">';
                    tr += $('#stt_'+item_id).val();
                    tr += '</td>';
                    tr += '<td id="item_'+item_id+'">';
                    tr += $('#item_'+item_id).val();
                    tr += '</td>';
                    tr += '<td align="right" id="quantity_'+item_id+'">';
                    tr += $('#quantity_'+item_id).val();
                    tr += '</td>';
                    tr += '<td align="right" id="price_'+item_id+'">';
                    tr += $('#price_'+item_id).val();
                    tr += '</td>';
                    tr += '<td align="right" id="discount_'+item_id+'">';
                    tr += $('#discount_'+item_id).val();
                    tr += '</td>';
                    tr += '<td align="right" id="total_'+item_id+'">';
                    tr += $('#total_'+item_id).val();
                    tr += '</td>';
                    $('#row_item_'+item_id).html(tr);
        }
   function hide_inline_item_term(item_id) {
                    var tr = '';
                    tr += '<td align="center">';
                    tr += '<input type="checkbox" value="'+item_id+'" name="item_check_list" id="item_check_list">';
                    tr += '<a href="#" class="icon-edit icon-all" onclick="edit_inline_term('+item_id+'); return false;" href="#" title="Inline Edit" style="margin-right:0px"></a>';
                    tr += '</td>';
                    tr += '<td align="center" id="stt_'+item_id+'">';
                    tr += $('#stt_'+item_id).val();
                    tr += '</td>';
                    tr += '<td id="item_'+item_id+'">';
                    tr += $('#item_'+item_id).val();
                    tr += '</td>';
                    $('#row_item_'+item_id).html(tr);
        }
        function save_inline_edit(item_id) {
            
            var template_id = $('#template_id').val();
            var item_name = $('#item_'+item_id).val();
            var item_quantity = $('#quantity_'+item_id).val();
            var item_price = $('#price_'+item_id).val();
            var item_discount = $('#discount_'+item_id).val();
            if(item_name != '') {
                $.ajax({
                    type: "POST",
                    data: "template_id="+template_id+"&item_id="+item_id+"&templ_item_name="+item_name+"&templ_item_quan="+item_quantity+"&templ_item_price="+item_price+"&templ_item_discount="+item_discount,
                    url: "?m=sales&a=vw_template&c=save_edit_item&suppressHeaders=true",
                    success: function(data) {
                            loadTab_item(template_id);
                    },
                    error: function () {
                        window.alert('Erreur de transmission au serveur');
                    }
                });
                } else {
                    alert('Please enter item name');
                }
        }
        function save_inline_edit_term(item_id) {
$.ajax({
                type: "POST",
                url: "?m=sales&a=vw_template&c=save_item_term_condition&suppressHeaders=true"+item_id,
                data: "",
                success: function (x) {
                    $('#div-popup').html(x).dialog({
                                    resizable: true,
                                    modal: true,
                                    title: '<?php echo $AppUI->_('Edit Term And Condition'); ?>',
                                    width: 520,
                                    maxheight: 400,
                                    close: function(ev, ui) {
                                        $('#div-popup').dialog('destroy');
                                    },
                                    buttons: {
                                    'Cancel': function() {
                                        $(this).dialog('close');
                                    },
                                    'Save': function() {
                                            var term_default = 0;
                                            if($("#term_default").is(':checked')){
                                                term_default = 1;
                                            }
                                            var term_and_condition = $('#term_and_condition').val();
                                            term_and_condition = term_and_condition.replace(/&/g, "%26")
                                            var template_type = $('#template_type').val();
                                            if (term_and_condition != '') {
                                            $.ajax({
                                                     type: "POST",
                                                     data: "term_and_condition="+term_and_condition+"&template_type="+template_type+"&term_default="+term_default+item_id,
                                                     url: "?m=sales&a=vw_template&c=_do_edit_term_condition&suppressHeaders=true",
//                                                     dataType: "json",
                                                     success: function(html){
//                                                            var template_id = html.templ_id;
//                                                            alert(template_id);
                                                                $('#div-popup').dialog('close');
//                                                                load_template_theme(html.templ_id);
                                                                term_condition_manager();
                                                         
                                                     },
                                                     error: function() {
                                                         window.alert('Erreur de transmission au serveur.')
                                                     }
                                                  });
                                            } else {
                                                alert('Please enter item name.');
                                            }
                                        }
                                    }
                            });
                            $('#div-popup').dialog('open');
                }
           });
        }
        function edit_template() {
            var template_id = get_Template_checked('check_list_quo', 'template_id');
            var template_split = template_id.split('&template_id[]=');
            if (template_id == '') {
                alert('Please choose Template.');
            } else if((parseInt(template_split.length)-1) > 1){
                alert('Sorry the system allows choose template.');
            } else {
                do_edit_template(template_id);
            }
        }
        function do_edit_template(template_id) {
            $.ajax({
                type: "POST",
                url: "?m=sales&a=vw_template&c=_load_edit_template&suppressHeaders=true"+template_id,
                data: "",
                success: function (x) {
                    $('#div-popup').html(x).dialog({
                                    resizable: true,
                                    modal: true,
                                    title: '<?php echo $AppUI->_('Edit Template'); ?>',
                                    width: 400,
                                    maxheight: 400,
                                    close: function(ev, ui) {
                                        $('#div-popup').dialog('destroy');
                                    },
                                    buttons: {
                                    'Cancel': function() {
                                        $(this).dialog('close');
                                    },
                                    'Save': function() {
                                            var template_name = $('#templ_name').val();
                                            template_name = template_name.replace(/&/g, "%26")
                                            var template_type = $('#template_type').val();
                                            var template_id = $('#template_id').val();
                                            if (template_name != '') {
                                            $.ajax({
                                                     type: "POST",
                                                     data: "template_name="+template_name+"&template_type="+template_type+"&template_id="+template_id,
                                                     url: "?m=sales&a=vw_template&c=_do_edit_template&suppressHeaders=true",
//                                                     dataType: "json",
                                                     success: function(html){
//                                                            var template_id = html.templ_id;
//                                                            alert(template_id);
                                                                $('#div-popup').dialog('close');
//                                                                load_template_theme(html.templ_id);
                                                                back_list_template();
                                                         
                                                     },
                                                     error: function() {
                                                         window.alert('not insert.')
                                                     }
                                                  });
                                            } else {
                                                alert('Please enter name Template.');
                                            }
                                        }
                                    }
                            });
                            $('#div-popup').dialog('open');
                }
            });
        }
        
        function term_condition_manager() {
            $('#tempalte').html('Loading...');
            $('#tempalte').load('?m=sales&a=vw_template&c=template_term_condition&suppressHeaders=true');
        }
        function insert_term_condition(template_id){
            $.ajax({
                type: "POST",
                url: "?m=sales&a=vw_tab_term&c=_load_term_condition&suppressHeaders=true&template_id="+template_id,
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
                                                    var current_textarea ="";
                                                    if($('#term_condition').val()!="")
                                                    current_textarea = $('#term_condition').val()+'\n';
                                                    //var textAreaAttitude = getTextArea();
                                                    var textAreaAttitude = $(this).val();
                                                        if (textAreaAttitude != '') {
                                                            $('#term_condition').val(current_textarea + textAreaAttitude);
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
            var checked = [];
            $("input[name='check_template[]']:checked").each(function (i)
            {
//                checked.push(parseInt($(this).val()));
                checked[i] = $(this).val();
                //alert(checked[i]);
            });
            
        var textAreaAttitude = checked;
        return textAreaAttitude;
    }
    function check_termDefault(term_id,value_default){
       var term_default=0;
       if(value_default==0){
           term_default =1;
       }
//       alert(term_default);
                    $.ajax({
                       type: "POST",
                       url: '?m=sales&a=vw_template&c=_do_edit_term_condition&suppressHeaders=true',
                       data: 'term_condition_id='+term_id+'&term_default='+term_default,
                       success: function (data) {
                           term_condition_manager();
                       },
                       error: function() {
                           window.alert('fail.');
                       }
                    });
    }
    
    function loadtab_sub_heading(template_id){
        $('#tab_detail').html('Loading...');
        $('#tab_detail').load('?m=sales&a=vw_tab_subheading&suppressHeaders=true', {template_id: template_id});
    }
    
    function save_subheading(template_id,subheading_id)
    {
        var subheading_content = $('#template_subheading').val();
        //alert(subheading_content);
        $.ajax({
            type: "POST",
            url: '?m=sales&a=vw_template&c=_do_save_subheading_template&suppressHeaders=true',
            data: {template_id:template_id,subheading_id:subheading_id,subheading_content:subheading_content},
            success: function(){
                loadtab_sub_heading(template_id);
            },
            error: function(){
                alert('error');
            }
        });
    }
</script>
