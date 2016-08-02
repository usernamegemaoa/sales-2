<?php

if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

// deny all but system admins
$canEdit = getPermission('system', 'view');
if (!$canEdit) {
	$AppUI->redirect("m=public&a=access_denied");
}

        $AppUI->savePlace();
// setup the title block
// Neu nhieu config co the tao tab

require_once (DP_BASE_DIR."/modules/sales/CTax.php");
require_once (DP_BASE_DIR."/modules/sales/CTemplatePDF.php");

$CTax = new CTax();
$CTemplatePDF = new CTemplatePDF();

function __default() {
    
    global $a, $m;
    
        $titleBlock = new CTitleBlock('Configure Sales Module', 'sales-48.png', $m, "$m.$a");
        $titleBlock->show();
        
        $tabBox = new CTabBoxJQ('?m=sales'.$task_id, '' , 0 );
        $tabBox->setTabBoxID("tabs-view-" . $m);
        $tabBox->setModuleName($m);

        $tabBox->add(DP_BASE_DIR . '/modules/sales/configure&c=_vw_tax', 'Tax');
        $tabBox->add(DP_BASE_DIR . '/modules/sales/configure&c=_vw_upload_logo', 'Upload Logo');
        $tabBox->add(DP_BASE_DIR . '/modules/sales/configure&c=_vw_create_supplier', 'Config Supplier');
        $tabBox->add(DP_BASE_DIR . '/modules/sales/configure&c=_view_template_pdf','Template PDF');
        $tabBox->show();
        
    
}

function _vw_tax() {
    
        require_once (DP_BASE_DIR."/modules/sales/tax.js.php");
        
        echo '<div id="div_tax_cofig">';
                show_html_table();
        echo '</div>';
}

function show_html_table() {

        global $AppUI, $CTax;

        
        echo '<br/><a onclick="delete_tax(); return false;" href="#" class="icon-delete icon-all">Delete</a>';
        echo '<a onclick="set_tax_default(); return false;" href="#" class="icon-edit icon-all">Set to Default</a><br/><br/>';
        //echo '<a onclick="add_tax(1);" href="#" class="icon-add" id="add-tax-click">Add tax</a><br/><br/>';
        
        $dataTableRaw = new JDataTable('tax_table');
        $dataTableRaw->setWidth('60%');
        $dataTableRaw->setHeaders(array(
            '<input name="select_all" id="select_all" type="checkbox" onclick="check_all(\'select_all\', \'check_list\');">',
            'Name',
            'Rate',
            'Added by'
            ));

        $colAttributes = array(
            'class="checkbox" width="5%" align="center"',
            'class="edit_tax_name" align="center" width="10%"',
            'class="edit_tax_rate" width="20%"',
            'width="20%"',
        );

        $tableData = array(); $rowIds = array();
        
        $tax_arr = $CTax->list_tax();
        if (count($tax_arr) > 0) {
            $default_text = array(0 => '', 1 => '<font color="red" size="2" title="default">**</font>');
            foreach ($tax_arr as $tax) {
                $tableData[]= array(
                    '<input type="checkbox" id="check_list" name="check_list" value="'. $tax['tax_id'] .'">',
                    $tax['tax_name'],
                    $tax['tax_rate'],
                    dPgetUsername($tax['user_id']). ' ' . $default_text[$tax['tax_default']],
                );
                $rowIds[] = $tax['tax_id'];
            }
        }

        $dataTableRaw->setDataRow($tableData, $colAttributes, $rowIds);
        $dataTableRaw->setJEditable('edit_tax_name', '?m=sales&a=configure&c=_do_update_tax_field&suppressHeaders=true&col_name=tax_name');
        $dataTableRaw->setJEditable('edit_tax_rate', '?m=sales&a=configure&c=_do_update_tax_field&suppressHeaders=true&col_name=tax_rate');
                
        $dataTableRaw->setTableAttributes('class="tbl" cellpadding="2" cellspacing="1"');
        $dataTableRaw->show();
        
        $js = 
<<<EOD
        $(document).ready(function() {
            var tr = '<tfoot>';
            tr += '<td>';
            tr += '</td>';
            tr += '<td>';
            tr += '<input type="text" id="tax_name" name="tax_name" class="required text"/>';
            tr += '</td>';
            tr += '<td>';
            tr += '<input type="text" id="tax_rate" name="tax_rate" class="required text" />';
            tr += '</td>';
            tr += '<td>';
            tr += '<button id="btn-add-tax" class="ui-button ui-state-default ui-corner-all" onclick="save_tax(); return false;">Submit</button>';
            tr += '</td>';
            tr += '</tr>';
            tr += '</tfoot>';
            $('#tax_table').append(tr);
            
        });
EOD;
        
        $AppUI->addJSScript($js);

}

function _vw_upload_logo() {
    
        echo '<div id="div_logo_cofig">';
                _vw_upload_logo_form();
        echo '</div>';
}


function _vw_upload_logo_form() {
    $url = DP_BASE_DIR. '/modules/sales/images/logo/';
    $base_url = DP_BASE_URL. '/modules/sales/images/logo/';
    
    $files = list_files($url);
    if(count($files)>0)
    {
        $i = count($files)-1;
            echo '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="100" width="150" /><br/>';
            echo '<a class="icon-delete icon-all" onclick="remove_logo(\''. $path_file .'\'); return false;" href="#">Delete</a>';  
    }
    
//    $files = scandir($url);
////    print_r($files);
//    $i=count($files)-1;
//    if (count($files) > 2) {
//        $path_file = $url . $files[$i];
////        echo $files[$i];
//        if($files[$i]!=".svn"){
//            echo '<img src="'. $base_url . $files[$i] .'" alt="Smiley face" height="100" width="150" /><br/>';
//            echo '<a class="icon-delete icon-all" onclick="remove_logo(\''. $path_file .'\'); return false;" href="#">Delete</a>';
//        }
//    }
    
    echo '<form id="logo_frm" name="logo_frm" onsubmit="return validate_form();" method="post" action="?m=sales&a=configure&c=_do_upload_logo" enctype="multipart/form-data">';
    echo '<b>Files</b> must be less than 6 MB. <b>Allowed file types:</b> png, jpg, jpeg, bmp.<br/>';
    echo '<input type="file" name="logo" id="logo"/> ';
    echo '<input type="hidden" name="path_file" id="path_file" value="'. $path_file . '" /> ';
    echo '<button class="ui-button ui-state-default ui-corner-all">Upload</button><br/>';
    echo '</form>';
}

function _do_upload_logo() {
    
    global $AppUI;


    $dir = $_FILES['logo']['tmp_name'];
        $file_name = $_FILES['logo']['name'];
        $file_name = strtolower(str_replace(" ", "", $file_name));
        if ($file_name != null || $file_name != '') {

            $allowed_file = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
            $file_type_arr = explode('.', $file_name);
            $file_type = $file_type_arr[count($file_type_arr) - 1];

            if (in_array($file_type, $allowed_file)) {

                $file_size = @filesize($dir);
                
                if ($file_size <= 6291456) { // > 6mb

                    $file_name_format = $AppUI->user_id . '_' . $file_name;
                    $url = DP_BASE_DIR. '/modules/sales/images/logo/';
                    $url_file = $url . $file_name_format;
                    
                    if (is_writable($url)) {
                        $upload_success = move_uploaded_file($dir, $url_file);
                        if ($upload_success) {
                            if ($_POST['path_file'] != '' || $_POST['path_file'] != null) {
                                $_POST['url'] = $_POST['path_file'];
                                _do_delete_logo();
                            }
                            $AppUI->setMsg($AppUI->_('Upload success!'), 1);
                            $AppUI->redirect('m=sales&a=configure');
                        } else {
                            $AppUI->setMsg($AppUI->_('Upload error!'), 3);
                            $AppUI->redirect('m=sales&a=configure');
                        }
                    } else {
                            $AppUI->setMsg($AppUI->_('System disk is not writable. Permission current: '. substr(sprintf('%o', fileperms($url)), -4) .'"}'), 3);
                            $AppUI->redirect('m=sales&a=configure');
                    }
                } else {
                    $AppUI->setMsg($AppUI->_('File size cannot be greater than 6mb.', 3));
                    $AppUI->redirect('m=sales&a=configure');                
                }
            } else {
                    $AppUI->setMsg($AppUI->_('File type required: jpg, jpeg, png, bmp.'), 3);
                    $AppUI->redirect('m=sales&a=configure');
            }
        } else {
                $AppUI->setMsg($AppUI->_('Please choose a file.'), 3);
                $AppUI->redirect('m=sales&a=configure');
        }
}

function _do_delete_logo() {
    unlink($_POST['url']);
    echo '{"status": "Success"}';
    
}

function do_add_tax() {
    
        global $AppUI, $CTax;
        $_POST['user_id'] = $AppUI->user_id;
        $tax_id = $CTax->add_tax($_POST);

        if ($tax_id > 0) {
            echo "success";
        } else {
            echo "Failed, message: '" . $tax_id . "'";
        }
}

function _do_delete_tax() {
    
    global $CTax;

    $tax_id_arr = $_REQUEST['tax_id'];

    $db_return = $CTax->remove_tax($tax_id_arr);

    if ($db_return)
        echo '{"status": "Success"}';
    else
        echo '{"status": "Failure", "message": "Co loi trong qua trinh xoa"}';
}

function _do_update_tax_field() {

    global $CTax;

    $id = dPgetCleanParam($_POST, 'id');
    $updated_content = dPgetCleanParam($_POST, 'value');

    $col_name = dPgetCleanParam($_GET, 'col_name', '');

    $msg = $CTax->update_tax($id, $col_name, $updated_content);
    
    if (!$msg) {
        echo $updated_content;
    }
    
}

function _do_update_tax_default() {

    global $CTax;

    $id = $_REQUEST['tax_id'];
    $col_name = 'tax_default';
    
    $q = new DBQuery();
    $q->addTable('sales_tax');
    $q->addQuery('tax_id');
    $q->addWhere('tax_default = 1');
    $rows = $q->loadList();
    if ($rows) {
        $msg1 = $CTax->update_tax($rows[0]['tax_id'], $col_name, 0);
    }
    
    $msg2 = $CTax->update_tax($id[0], $col_name, 1);
    
    if (!$msg2)
        echo '{"status": "Success"}';
    else
        echo '{"status": "Failure", "message": "Co loi trong qua update"}';
    
}
function _vw_create_supplier(){
    global $AppUI;
    require_once (DP_BASE_DIR."/modules/system/owner_system_info.class.php");
    $owner = owner_system_info::get_owner_system_info();
        echo '<a  href="?m=system&a=create_owner_system_info" class="icon-edit icon-all">Edit Supplier</a><br/><br/>';
	$output = '<tr>
			<td class="item detailRowLabel">Name:</td>
            		<td align="left" class="detailRowField">'.$owner['sales_owner_name'].'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">Address 1</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_address1'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">Address 2:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_address2'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">Phone 1:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_phone1'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">Phone 2:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_phone2'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">Email:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_email'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">City:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_city'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">State:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_state'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">Country:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_country'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">Postal code:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_postal_code'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">Website:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_website'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">Reg No:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_reg_no'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">GST Reg No:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_gst_reg_no'] .'</td>
                    </tr>';
	$output .= '<tr>
			<td class="item detailRowLabel">Fax:</td>
            		<td align="left" class="detailRowField">'. $owner['sales_owner_fax'] .'</td>
                    </tr>';
    
    echo '<form name="owner_frm" action="index.php?m=system&a=create_owner_system_info&c=_do_add_owner_system" method="post">
        <table cellspacing="0" cellpadding="3" border="0" class="std" width="100%" align="center">
                '. $output .'
        </table>
    </form>';
}
function _view_template_pdf()
{
    global $AppUI;
    echo '<div id="button_template_pdf" style="margin-bottom:20px;">';
        echo  '<button class="ui-button ui-state-default ui-corner-all" onclick="list_view_template_pdf(); return false">View template PDF</button>&nbsp;';
        echo  '<button class="ui-button ui-state-default ui-corner-all" onclick="list_footer_template_pdf(); return false">Footer text template</button>&nbsp;';
    echo '</div>';
    echo '<div id="sales_template_pdf">';
        _vw_template_pdf();
    echo '<div>';
    
        $js = 
<<<EOD
                $(document).ready(function() {
                       var url = '?m=sales&a=vw_payment&c=_do_update_payment_field&suppressHeaders=true';
                       var url1 = '?m=sales&a=vw_payment&c=_do_update_payment_detail_field&suppressHeaders=true';

                       $('.footer_text_invoice').editable(url, {
                           indicator    : "Loading...",
                           submitdata : { field_name: 'payment_notes' },
                           type: 'textarea',
                           submit: "Save",
                           cancel: 'Cancel',
                       });
                });
                
                function list_view_template_pdf()
                {
                    $('#sales_template_pdf').load('?m=sales&a=configure&c=_vw_template_pdf&suppressHeaders=true');
                }
                function list_footer_template_pdf()
                {
                    $('#sales_template_pdf').load('?m=sales&a=configure&c=_vw_foote_template_pdf&suppressHeaders=true');
                }
                function activate_template_pdf(value)
                {
                   $.post('?m=sales&a=configure&c=_update_template_pdf&suppressHeaders=true',{template_pdf_id:value},
                       function(data){
                            $('#ui-tabs-4').load('?m=sales&a=configure&c=_view_template_pdf&suppressHeaders=true');
                           });
                }
                function popup_template_pdf(template_id)
                {
                   $('#pop-up-'+template_id).show();
                   $('#div-pop-up').show();
                   $("#pop-up-"+template_id).css('top','0').css('left','160');
                   return false;
                }
                function hide_popup_template_pdf(template_id)
                {
                    $("#pop-up-"+template_id).hide();
                    $('#div-pop-up').hide();
                    return false;
                }
EOD;
        
        $AppUI->addJSScript($js);
}
function _vw_foote_template_pdf()
{
    global $AppUI,$CTemplatePDF;
    $template_arr = $CTemplatePDF->get_template_pdf();
    $i=0;
    //echo '<a id="add-new" class="icon-add icon-all" href="#" onclick="edit_footer_text();">Edit footer text</a><br><br>';
    echo '<table border="0" width="70%" class="tbl" cellspacing="1" cellpadding="2">';
        echo"<tr>
                <th width='15'></th>
                <th>Invoice's Footer Text</th>
                <th>Template</th>
             </tr>";
        foreach ($template_arr as $value) {
            $i++;
            echo'<tr id="tr_template_pdf_'.$value['template_pdf_id'].'">
                    <td align="center"><a id="add-new" class="icon-edit icon-all" href="#" onclick="edit_footer_text('.$value['template_pdf_id'].');return false;"></a>
                    <td id="'.$value['template_pdf_id'].'">
                        '.nl2br($value['footer_text_invoice']).'
                        <div class="footer_text_invoice_'.$value['template_pdf_id'].'" style="display:none;">'.$value['footer_text_invoice'].'</div>
                    </td>
                    <td align="center">Template '.$value['template_pdf_id'].'</td>
                </tr>';
        }
    echo '<table>';
    
       $js = 
<<<EOD
       function edit_footer_text(item_id)
       {
               var footer_text = $('.footer_text_invoice_'+item_id).html();
               var tr = "";
               tr+='<td width="70"><a  href="#" onclick="list_footer_template_pdf(); return false;">Cancel</a></td>';
               tr+="<td>";
               tr+="<textarea id='footer_text_template' rows='3' cols='50'>"+footer_text+"</textarea>";
               tr+="<input type='button' value='Save' onclick='save_footer_pdf("+item_id+");return false;' />";
               tr+="</td>";
               tr+="<td>Template "+item_id+"</td>";
               
               $('#tr_template_pdf_'+item_id).html(tr);
       }
       function save_footer_pdf(item_id)
       {
               var footer_text = $('#footer_text_template').val();
               $.post('?m=sales&a=configure&c=_do_update_footer_text_template&suppressHeaders=true',{id:item_id,footer_text:footer_text},
                        function(data){
                            list_footer_template_pdf();
                        }
                   )
       }
                
EOD;
        
        $AppUI->addJSScript($js);
}
function _vw_template_pdf()
{
    global $AppUI,$CTemplatePDF;
    $template = dPgetSysVal('TemplatePDF');
    $template_arr = $CTemplatePDF->get_template_pdf();
    $i=0;
    echo '<div style="width:100%;overflow:hidden;">';
    foreach ($template_arr as $value) {
        $i++;
        $checked="";
        if($value['template_default']==1)
        {
            $checked = "checked";
        }
        echo'<div style="width:250px;float:left;overflow:hidden;text-align:center;margin-bottom:30px;">
                <img onclick="popup_template_pdf('.$value['template_pdf_id'].');" src="'.$value['template_pdf_images'].'" width="200" style="cursor: pointer; padding:1px;border:1px solid #021a40;" /><br>
                <b>Template '.$i.'</b><br>
                <input type="radio" name="activate" value="'.$value['template_pdf_id'].'" '.$checked.' onclick="activate_template_pdf(this.value);return false;">Active

                <div id="pop-up-'.$value['template_pdf_id'].'" style="display: none;position: fixed;padding: 10px;z-index:100;width:960px;">
                    <div id="box-content"><a id="box-close" href="#" onclick="hide_popup_template_pdf('.$value['template_pdf_id'].')"></a></div>
                    <img src="'.$value['template_quo_images_pdf'].'" width="450" style=" padding:1px;border:1px solid #021a40;position: relative;top:-15px;margin-right:20px;" />
                    <img src="'.$value['template_pdf_images'].'" width="450" style=" padding:1px;border:1px solid #021a40;position: relative;top:-15px;" />
                </div>
            </div>';
    }
    echo '</div>';
    echo '<div id="div-pop-up" style="display: none;z-index: 10;position: fixed;width: 100%;height:100%;padding: 0px;background: #777;top:0;left:0;opacity:0.7;">
            
        </div>';
    
echo '<style>
        #box-content{
             left: 465px;
             position: relative;
             top: 4px;
             width: 50px;
             height: 35px;
             z-index: 100;
        }
        #box-close{
            background:url("./modules/sales/images/icon/fancybox_sprite.png") repeat scroll -40px 0 rgba(0, 0, 0, 0);
            padding: 12px 24px 6px 21px;
            position: relative;
            top: 11px;
        }
    </style>';
}

function _update_template_pdf()
{
    global $CTemplatePDF;
    $template_arr = $CTemplatePDF->get_template_pdf();
    foreach ($template_arr as $value) {
        $template_pdf = $CTemplatePDF->update_template_pdf($value['template_pdf_id'], 0);
    }
    $template_pdf_id = $_POST['template_pdf_id'];
    if($CTemplatePDF->update_template_pdf($template_pdf_id, 1))
    {
        echo "success";
    };
}
function _do_update_footer_text_template() {
    global $CTemplatePDF;
    $id = $_POST['id'];
    $footer_text = $_POST['footer_text'];

    $CTemplatePDF->update_template_pdf_footer($id, $footer_text);
    
    echo $value;
}
?>

