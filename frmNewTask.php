<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

//echo "<script src='js/chosen.jquery.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' type='text/css' href='style/default/chosen.css'/>";

    require_once (DP_BASE_DIR. "/modules/clients/company_manager.class.php");
    require_once (DP_BASE_DIR. "/modules/clients/company.class.php");
    require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
    require_once (DP_BASE_DIR. '/modules/admin/user_designation.class.php');
    require_once (DP_BASE_DIR. '/modules/admin/admin.class.php');
    require_once (DP_BASE_DIR. '/modules/contacts/contacts.class.php');
    require_once (DP_BASE_DIR."/modules/contacts/contacts_manager.class.php");
    require_once (DP_BASE_DIR."/modules/jobs/jobManager.class.php");
    require_once (DP_BASE_DIR."/modules/serviceOrders/ServiceOrderManager.php");
    require_once (DP_BASE_DIR.'/modules/jobs/CJobSetting.class.php');
    require_once(DP_BASE_DIR. '/modules/equipments/equipmentManager.class.php');

    global $AppUI;

    $perms =& $AppUI->acl();
    $user_id = $AppUI->user_id;
    $user_roles_arr = $perms->getUserRoles($user_id);
    if (count($user_roles_arr) >0) {
        $user_roles = array();
        foreach ($user_roles_arr as $user_roles_id) {
            $user_roles[] = $user_roles_id['value'];
        }
    }

    $cconfig = new CConfigNew();
    $job_status = $cconfig->getRecordConfigByList('job status');

    $services = $cconfig->getRecordConfigByList('service');

    $cManager = new CompanyManager;
    $JobManager = new JobManager();
    $serviceOrder = new ServiceOrderManager();
    $obj_setting = new CJobSetting();
    $eqManager = new EquipmentManager();
    
    $job_id = 0; 
    $start_date = $end_date = date('d/m/Y'); $date_value = date('Ymd');
    $option_equipment ="";
    if($_POST['customer_id'] && $_POST['customer_id']>0){
        $company_id = $_POST['customer_id'];
        $job_location_id = $_POST['job_location_id'];
        $quotation_id = $_POST['quotation_id'];
        $note_address = ''; $option_location = '';
        $addresses = $JobManager->get_list_address_by_customer($company_id);
        if (count($addresses) > 0) {
            foreach ($addresses as $ad) {
                $address_html = '';
                $addtype = dPgetSysVal('AddressType');
                if ($ad['address_type'] == '' || $ad['address_type'] == NULL)
                    $address_html .= '[NA] ';
                else
                    $address_html .= '['.$addtype[$ad['address_type']].'] ';
                if ($ad['address_branch_name'] != '' && $ad['address_branch_name'] != NULL)
                    $address_html .= $ad['address_branch_name'].': ';
                $address_html .= $ad['address_street_address_1'];
                ///////
                $select = "";
                if ($ad['address_id'] == $job_location_id)
                    $select="selected";
                $option_location .= '<option value="'. $ad['address_id'] .'" '.$select.' title="'. stripcslashes($address_html) .'">'. stripcslashes($address_html) .'</option>';
                // customer note
                $customer_notes = ''; 
                $company = $cManager->getCompanies($company_id);
                if ($company['company_note'] != '') {
                    $customer_notes = 'General Note: ' . $company['company_note']; 
                }
                $option_contact ="";
                $contact_address_arr = $cManager->getContactInAddress($job_location_id);
                if (count($contact_address_arr) > 0) {
                    foreach ($contact_address_arr as $cd) {
                        $option_contact .= '<option value="'. $cd['contact_id'] .'">'. $cd['contact_last_name'] .', '.$cd['contact_first_name'] .'</option>';
                    }
                }
                
                // option equipment
                $option_equipment = '';
                $equipment_address = $eqManager->get_equipment(false, $job_location_id);
                if (count($equipment_address) > 0) {
                    foreach ($equipment_address as $equipment) {
                        $option_equipment .= '<option value="'. $equipment['equipment_id'] .'" selected="selected">'. $equipment['equipment_location'] .'/'.$equipment['equipment_brand'].'/'.$equipment['equipment_model'].'/'.$equipment['equipment_serial_no'] .'</option>';
                    }

                }
                //echo $option_equipment;
            }
        }
        
    }
    
    if (isset($_POST['job_id']) && $_POST['job_id'] > 0) { // neu click tu ben calendar
        $job_id = $_POST['job_id'];
        $job_arr = $JobManager->getJobs($job_id);
        $company_id = $job_arr['job_company_id'];
        $service_id = $job_arr['job_service_id'];
        $start_time = $job_arr['job_start_time'];
        $end_time = $job_arr['job_end_time'];
        
        $start = $JobManager->formatTimeToFloat($start_time);
        $end = $JobManager->formatTimeToFloat($end_time);
        $duration = $end - $start;
        
        $start_date = date('d/m/Y', strtotime($job_arr['job_start_date']));
        $end_date = date('d/m/Y', strtotime($job_arr['job_work_deadline']));
        $date_value = date('Ymd', strtotime($job_arr['job_start_date']));
        
        $notes = preg_replace("/<br>/i", "
", $job_arr['job_note']);
//        $notes = $job_arr['job_note'];
        $team_id = $job_arr['team_id'];
        $status_id = $job_arr['job_status_id'];
        
        //update by luyenvt 11/01/2013
        $template_job_arr = $serviceOrder->get_job_service_order(false, $job_id);
        $template_id_arr = array();
        if (count($template_job_arr) > 0) {
            foreach ($template_job_arr as $so_template) {
                $template_id_arr[] = $so_template['template_id'];
            }
        }
        // end update
        $ass_job = $JobManager->getAssignees(false, $job_id);
        if (count($ass_job) > 0) {
            foreach ($ass_job as $ass) {
                $ass_job_arr[] = $ass['assignee_user_id'];
            }
        }
        
        //  09/12/2013 - load loaction
        $note_address = ''; $option_location = '';
        $addresses = $JobManager->get_list_address_by_customer($company_id);
        $address_arr_job_id = $JobManager->get_list_address_by_job($job_id);
        if ($address_arr_job_id) {
            $address_id_arr = array();
            foreach ($address_arr_job_id as $address_id) {
                $address_id_arr[] = $address_id['address_id'];
            }
        }
        // option contact
        $contact_id_arr = array(); $option_contact = ''; 
        $contact_arr = $JobManager->getJobContact(false, $job_id);
        if (count($contact_arr) > 0) {
            foreach ($contact_arr as $contact_id) {
                $contact_id_arr[] = $contact_id['contact_id'];
            }
        }
        //$contact_id_arr[] = $_POST['contact_id']; 
        // option equipment
        $equipment_id_arr = array(); $option_equipment = '';
        $equipment_arr = $eqManager->getEquipmentJob(false, false, $job_id);
        if (count($equipment_arr) > 0) {
            foreach ($equipment_arr as $equipment_id) {
                $equipment_id_arr[] = $equipment_id['equipment_id'];
            }
        }
        if (count($addresses) > 0) {
            foreach ($addresses as $ad) {
                $address_html = '';
                $addtype = dPgetSysVal('AddressType');
                if ($ad['address_type'] == '' || $ad['address_type'] == NULL)
                    $address_html .= '[NA] ';
                else
                    $address_html .= '['.$addtype[$ad['address_type']].'] ';
                if ($ad['address_branch_name'] != '' && $ad['address_branch_name'] != NULL)
                    $address_html .= $ad['address_branch_name'].': ';
                $address_html .= $ad['address_street_address_1'];
                if (!isset($address_arr_job_id)) {
                        $option_location .= '<option value="'. $ad['address_id'] .'" title="'. stripcslashes($address_html) .'">'. stripcslashes($address_html) .'</option>';
                } else {
                    if (isset($address_arr_job_id) && count($address_arr_job_id) > 0 && in_array($ad['address_id'], $address_id_arr))
                        $option_location .= '<option value="'. $ad['address_id'] .'" selected="selected" title="'. stripcslashes($address_html) .'">'. stripcslashes($address_html) .'</option>';
                    else 
                        $option_location .= '<option value="'. $ad['address_id'] .'" title="'. stripcslashes($address_html) .'">'. stripcslashes($address_html) .'</option>';
                }
                    
                if (intval($ad['address_id']) > 0) {
                    $contact_address_arr = $cManager->getContactInAddress($ad['address_id']);
                    if (count($contact_address_arr) > 0) {
                        foreach ($contact_address_arr as $cd) {
                            if (count($contact_id_arr) > 0 && in_array($cd['contact_id'], $contact_id_arr)) 
                                $option_contact .= '<option value="'. $cd['contact_id'] .'" selected="selected">'. $cd['contact_last_name'] .', '.$cd['contact_first_name'] .'</option>';
                            else 
                                $option_contact .= '<option value="'. $cd['contact_id'] .'">'. $cd['contact_last_name'] .', '.$cd['contact_first_name'] .'</option>';
                        }
                    }
                    
                    $address_info = $cManager->getAddresses($ad['address_id']);
                    if ($address_info['address_notes'] != '')
                    $note_address .= '
'.'Address Note: '.$address_info['address_notes'];
                    
                    $equipment_address = $eqManager->get_equipment(false, $ad['address_id']);
                    if (count($equipment_address) > 0) {
                        foreach ($equipment_address as $equipment) {
                            if (count($equipment_id_arr) > 0 && in_array($equipment['equipment_id'], $equipment_id_arr)) 
                                $option_equipment .= '<option value="'. $equipment['equipment_id'] .'" selected="selected">'. $equipment['equipment_location'] .'/'.$equipment['equipment_brand'].'/'.$equipment['equipment_model'].'/'.$equipment['equipment_serial_no'] .'</option>';
                            else 
                                $option_equipment .= '<option value="'. $equipment['equipment_id'] .'">'. $equipment['equipment_location'] .'/'.$equipment['equipment_brand'].'/'.$equipment['equipment_model'].'/'.$equipment['equipment_serial_no'] .'</option>';
                        }

                    }
                }
            }
        }
        
        // customer note
        $customer_notes = ''; 
        $company = $cManager->getCompanies($company_id);
        if ($company['company_note'] != '') {
            $customer_notes = 'General Note: ' . $company['company_note']; 
        }
        if ($note_address != '')
            $customer_notes .= $note_address;
        // end update 09/12/2013
    }

    $active_id = $cconfig->getRecordConfigByName('active',1);

    if (in_array('agent', $user_roles) || in_array('customer', $user_roles) || in_array('tenant', $user_roles)) {
        $ContactManager = new ContactManager();
        $company_id_arr = array();
        $company_cont_arr = $ContactManager->getCompanyContactByUserId($AppUI->user_id);
        if (count($company_cont_arr) > 0) {
            foreach ($company_cont_arr as $company) {
                $company_id_arr[] = $company['company_id'];
            }
            $rows = $JobManager->get_list_customer($company_id_arr);
        }        
    } else {
        $rows = $cManager->getCompanies(false,$active_id,'all');
    }

    $start_job = $obj_setting->getAllRecordSetting(2);
    $TimeBlog = dPgetSysVal('TimeBlog');
    foreach ($TimeBlog as $key => $value) {
        if (isset($_POST['job_id']) && $_POST['job_id'] > 0) {
            if ($key == $start_time)
                $option_time_blog .= '<option value="'. $key .'" selected>'. $value .'</option>';
            else 
                $option_time_blog .= '<option value="'. $key .'">'. $value .'</option>';
        } else {
            if ($key == $start_job['job_setting_value'])
                $option_time_blog .= '<option value="'. $key .'" selected>'. $value .'</option>';
            else
                $option_time_blog .= '<option value="'. $key .'">'. $value .'</option>';
        }
    }
    ?>
<script language="javascript">
    $(document).ready(function(){
        bindDatePicker('startdate');	
//        load_address_dropdown();
        //loadStartdate();
        $("#task_customer_id").chosen(); 
        $('#task_customer_id_chzn').css({'width':'350px'});
        $('.chzn-search').css({'width':'300px'});
        $('#task_customer_id_chzn #input_search').css({'width':'300px'});
        var config_p = {
                '#sales_equipments'             : {},
                '#sales_location_id'             : {},
                '#task_contact_id'             : {}
        }
        for (var selector_p in config_p) {
            $(selector_p).chosen(config_p[selector_p]);
        }
        $('#sales_equipments_chzn').css({'width':'250px'});
        $('#sales_location_id_chzn').css({'width':'250px'});
        $('#task_contact_id_chzn').css({'width':'250px'});
        
        <?php
        if (in_array('partner', $user_roles) || in_array('coordinator', $user_roles)) {
        ?>
            var config = {
                    '#sales_member_id'              : {},
                    '#sales_service_order_temp'     : {},
                    '#sales_team_id'                : {}
            }
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
            $('#sales_member_id_chzn').css({'width':'250px'});
            $('#sales_service_order_temp_chzn').css({'width':'250px'});
            $('#sales_team_id_chzn').css({'width':'250px'});
            
            var b=document.getElementById("choice_field_print");

                b.onchange = function() {

                    if (b.type == 'checkbox' && b.checked == true) {
                        print_show();
                    } else {
                        print_hide();
                    }
                }
        <?php }?>
        $("input:radio[name=address_types]").click(function() {
            var client_id = $('#task_customer_id').val();
            var address_type = $(this).val();
            
            $('#address_type_values').val(address_type);
            if (client_id != 0) {
                $('#sales_location_id').load('?m=abms_resource_calendar&a=do_process&c=_do_load_address&suppressHeaders=true', { customer_id: client_id, address_type: address_type }, function(){
                    var address_id = $('#sales_location_id').val();
                    $("#sales_location_id").trigger("liszt:updated");
                    $('#sales_equipments').load('?m=abms_resource_calendar&a=do_process&c=_do_load_equipment_address&suppressHeaders=true', 'address_id='+address_id, function() {
                        $("#sales_equipments").trigger("liszt:updated");
                    });
                    $('#sales_customer_note').load('?m=abms_resource_calendar&a=do_process&c=_do_load_note_address&suppressHeaders=true', 'address_id='+address_id);
                    $('#task_contact_id').load('?m=abms_resource_calendar&a=do_process&c=_do_load_contact_byaddress&suppressHeaders=true', 'address_id='+address_id, function() {
                        $("#task_contact_id").trigger("liszt:updated");
                    });
                });
                
            }
        });
    });
    
    function load_address_dropdown() {
        if ($('#task_customer_id').val() != 0) {
            $('#sales_task_all').attr("checked","true");
            $('#address_type_values').val('all');
            
            var client_id = $('#task_customer_id').val();
            var address_type = $('#address_type_values').val();
            var job_id = <?php echo $job_id;?>
            
            $('#sales_location_id').load('?m=abms_resource_calendar&a=do_process&c=_do_load_address&suppressHeaders=true', { customer_id: client_id, job_id: job_id, address_type: address_type }, function(){
                var address_id = $('#sales_location_id').val();
                $("#sales_location_id").trigger("liszt:updated");
                $('#sales_equipments').load('?m=abms_resource_calendar&a=do_process&c=_do_load_equipment_address&suppressHeaders=true', 'address_id='+address_id+'&job_id='+job_id, function() {
                    $("#sales_equipments").trigger("liszt:updated");
                });
                $('#sales_customer_note').load('?m=abms_resource_calendar&a=do_process&c=_do_load_note_address&suppressHeaders=true', 'address_id='+address_id+'&job_id='+job_id);
                $('#task_contact_id').load('?m=abms_resource_calendar&a=do_process&c=_do_load_contact_byaddress&suppressHeaders=true', 'address_id='+address_id+'&job_id='+job_id, function() {
                    $("#task_contact_id").trigger("liszt:updated");
                });
            });
        }
    }	
    
    function load_contact_address_task() {
            if ($('#sales_location_id').val() != 0 && $('#sales_location_id').val() != '') {
                var address_arr = $('#sales_location_id').val(); 
                var job_id = $('#job_id').val();
                var mang_gia_tri = 'address_id='+address_arr+'&job_id='+job_id;

                $('#task_contact_id').load('?m=abms_resource_calendar&a=do_process&c=_do_load_contact_byaddress&suppressHeaders=true', mang_gia_tri, function() {
                    $("#task_contact_id").trigger("liszt:updated");
                });
                $('#sales_customer_note').load('?m=abms_resource_calendar&a=do_process&c=_do_load_note_address&suppressHeaders=true', mang_gia_tri);
                $('#sales_equipments').load('?m=abms_resource_calendar&a=do_process&c=_do_load_equipment_address&suppressHeaders=true', mang_gia_tri, function() {
                    $("#sales_equipments").trigger("liszt:updated");
                });
            }
    }
    
    var js_display_date_format="<?php echo (dPgetConfig('js_display_date_format')? dPgetConfig('js_display_date_format') : dPgetConfig('js_display_date_format_default'))?>";
    var js_input_date_format = "<?php echo (dPgetConfig('js_input_date_format') ? dPgetConfig('js_input_date_format') : dPgetConfig('js_input_date_format_default'))?>"
    function bindDatePicker(id)
    {
            var disp=id+'_display';
            var val=id+'_value';
            $('#'+disp).datepicker({
                    showOn: 'button', buttonImage: 'images/calendar.gif', buttonImageOnly: true,
                    dateFormat: js_display_date_format,
                    changeMonth: true,
                    changeYear: true,
                    onSelect: function(dateText) {
                            $('#'+val).val($.datepicker.formatDate(js_input_date_format, $(this).datepicker('getDate')));
                    } 
            });
    }
    
    function load_members() {
        var sales_team_id ="";
        var sales_team_id = $('#sales_team_id').val();
        //alert(sales_team_id);
        var job_id = $('#job_id').val();
        
        $.ajax({
                url:'?m=abms_resource_calendar&a=do_process&c=_do_load_assistant&suppressHeaders=true',
                data: {team_id: sales_team_id, job_id: job_id},
                type: 'POST',
                success:function(data){
                    $('#sales_member_id').html(data);
                    $("#sales_member_id").trigger("liszt:updated");
                }
            });
    }
    
    function loadStartdate() {
        var date = $('#abms-resource-calendar').fullCalendar('getDate');
        var da = new Date(date);
        var d = da.getDate();
        var m = da.getMonth()+1;
        var y = da.getFullYear();
        var result = '' + (d<=9?'0'+d:d) + '/' + (m<=9?'0'+m:m) + '/' + y;
        var value_date = '' + y + (m<=9?'0'+m:m) + (d<=9?'0'+d:d);
        var job_id = <?php echo $job_id;?>

        if (job_id == 0) {
            $('#sales_startdate_display').val(result);
            $('#sales_startdate_value').val(value_date);
        }
    }
    
    function print_show() {
            $('#sales_field_print').slideDown();
    }
    function print_hide() {
            $('#sales_field_print').slideUp(1000);
    }
    
    function add_new_tasks() {
            var job_id = $('#job_id').val();
            var client_id = $('#task_customer_id').val();
            var address_id = $('#sales_location_id').val();
            var contact_id = $('#task_contact_id').val();
            var service_id = $('#sales_service').val();
            var service_order_temp = $('#sales_service_order_temp').val();
            var status_id = $('#sales_status').val();
            var member_id = $('#sales_member_id').val();
            var equipment_id = $('#sales_equipments').val();
            var start_date = $('#sales_startdate_value').val();
            var start_time = $('#sales_start_time').val();
            var duration = $('#sales_duration').val();
            var team_id = $('#sales_team_id').val();
            var job_note = ''+$('#sales_note').val();
            var field_str= get_ID_checked('field_choose', 'field_name');
            var quo_service_id = $('#quotation_serviceoder').val();
            var quotation_id = "<?php echo $quotation_id; ?>";
            //alert (quotation_id);
            if (client_id == null || client_id == '') {
                    alert('<?php echo $AppUI->_('There are not any client! Please create a client before create job.')?>');
                    return false;
            } else if ((address_id == "") || (address_id == " ") || address_id == null) {
                    alert('<?php echo $AppUI->_('Please select a Location for job!')?>');
                    return false;
            } else if ((start_date == "") || (start_date == " ") || start_date == null) {
                    alert('<?php echo $AppUI->_('Please select a Date for job!')?>');
                    return false;
            } else if (duration == '' || duration == ' ' || isNaN(duration)) {
                    alert('<?php echo $AppUI->_('Please enter a number for duration of job!')?>');
                    return false;
//            } else if (team_id == '' || team_id == ' ' || team_id == null) {
//                    alert('<?php // echo $AppUI->_('Please select at least one Team!')?>');
//                    return false;
            }
            
            $('#saveJob').html('loading...');
            
                    $.post('?m=abms_resource_calendar&a=do_process&c=addNewJob&suppressHeaders=true',
                    {
                        client_id: client_id,
                        address_id: address_id,
                        service_id: service_id,
                        service_order_temp: service_order_temp,
                        startdate: start_date,
                        team_id: team_id,
                        duration: duration,
                        start_time: start_time,
                        status: status_id,
                        note: job_note,
                        contact_id: contact_id,
                        member_id: member_id,
                        equipment_id: equipment_id,
                        field_str: field_str, 
                        job_id: job_id,
                        //quotation_id: quotation_id
                    },
                    function(j){
                        if (j.status == 'success') {
//                            var date = j.message;
//                            var y = date.substr(0,4);
//                            var m = date.substr(4,2);
//                            var d = date.substr(6,2);
//
//                            $('#saveJob').html('Save');
//                            closeDialogPopup('task_popup-editTask');
//                            $('#task_popup-editTask').html('');
//                            load_Calender(y, m, d, 'resourceAgendaDay');
                            closeDialogPopup('sales_task_popup-editTask');
                            sales_done_serviceoder(quo_service_id,quotation_id,"quotation");

                        } else {
                            $('#saveJob').html('Save');
                            alert(j.status);
                        }
                    }, 'json');
    }
    
    function get_ID_checked(id_check, str_check) {
        var inputs = document.getElementsByName(id_check);
        var user_id = '';
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].type == 'checkbox' && inputs[i].checked == true) {
                user_id += inputs[i].value+',';
            }
        }
        return user_id;
    }
    
    function closeDialogPopup(id) {
            var id_='#'+id;
            $(id_).dialog('destroy');
    }
    
    function popupAddClient(id, message) {
            var id_='#'+id;
            
            $(id_).dialog({
                        resizable: false,
                        modal:true,
                        title: message,
                        close: function(ev,ui){$(id_).dialog('destroy');} 
                });
            $(id_).dialog('open');
    }
    
    function loadFrmEquipment() {
            var clientid = $('#task_customer_id').val();
            var address_id = $('#sales_location_id').val();
//            var frm = $('#frm_newEquipment').html();
            if (clientid == '') {
                alert('Please enter Customer');
            } else {
                    $.ajax({
                        url:'?m=clients&a=frmEquipment&suppressHeaders=true',
                        data: {client_id: clientid, address_id: address_id},
                        type: 'POST',
                        success:function(data){
                            $('#frm_newEquipment').html(data);
                        }
                    });
                    popupAddNew('frm_newEquipment', 'New Equipment');
            }
            
    }
    
    function popupAddNew(id, message) {
            var id_='#'+id;
            
            $(id_).dialog({
                        resizable: false,
                        modal:true,
                        title: message,
                        width: 'auto',
                        close: function(ev,ui){$(id_).dialog('destroy');} 
                });
            $(id_).dialog('open');
    }
    
    function addEquipment() {
            var equipment_note = $('#new_equipment_note').val();
            var equipment_location = $('#new_equipment_location').val();
            var equipment_brand = $('#new_equipment_brand').val();
            var equipment_model = $('#new_equipment_model').val();
            var equipment_serial = $('#new_equipment_serial').val();
            var equipment_type = $('#new_equipment_type').val();
            var equipment_date = $('#popup_date_value').val();
            var equipment_address = $('#address_id').val();
            var equipment_id = $('#equipment_id').val();
            var msg = '';
            
            if (equipment_location == '') {
                msg = '<?php echo $AppUI->_('Please enter equipment location.'); ?>';
                show_msg(msg, 'msg', 'err', 'show');
                return false;
            } 
            
        $.post('?m=clients&a=addedit_equipment&suppressHeaders=true', 
            {
                equipment_id: equipment_id,
                note: equipment_note,
                location: equipment_location,
                brand: equipment_brand,
                model: equipment_model,
                serial_no: equipment_serial,
                type: equipment_type,
                date: equipment_date,
                address: equipment_address,
                action: 'add'
            }, function(data){
                    if (data.status == 'success') {
                        closeDialogI('frm_newEquipment');
                        load_equipments();
                    } else {
                        alert(data.status);
                    }
            }, 'json'
        );
    }
    
    function addMultipleEquipment() {
            var equipment_note = $('#new_equipment_note').val();
            var equipment_location = $('#new_equipment_location').val();
            var equipment_brand = $('#new_equipment_brand').val();
            var equipment_model = $('#new_equipment_model').val();
            var equipment_serial = $('#new_equipment_serial').val();
            var equipment_type = $('#new_equipment_type').val();
            var equipment_date = $('#popup_date_value').val();
            var equipment_address = $('#address_id').val();
            var equipment_id = $('#equipment_id').val();
            var msg = '';
            
            if (equipment_location == '') {
                msg = '<?php echo $AppUI->_('Please enter equipment location.'); ?>';
                show_msg(msg, 'msg', 'err', 'show');
                return false;
            } 
            
        $.post('?m=clients&a=addedit_equipment&suppressHeaders=true', 
            {
                equipment_id: equipment_id,
                note: equipment_note,
                location: equipment_location,
                brand: equipment_brand,
                model: equipment_model,
                serial_no: equipment_serial,
                type: equipment_type,
                date: equipment_date,
                address: equipment_address,
                action: 'add'
            }, function(data){
                    if (data.status == 'success') {
                        alert('Add Equipment Success!');
                        load_equipments();
                        loadFrmEquipment();
                    } else {
                        alert(data.status);
                    }
            }, 'json'
        );
    }
    
    function load_equipments() {
            if ($('#sales_location_id').val() != 0 && $('#sales_location_id').val() != '') {
                var address_arr = $('#sales_location_id').val(); 
                var mang_gia_tri = 'address_id='+address_arr;

                $('#sales_equipments').load('?m=abms_resource_calendar&a=do_process&c=_do_load_equipment_address&suppressHeaders=true', mang_gia_tri, function() {
                    $("#sales_equipments").trigger("liszt:updated");
                });
            }
    }
    
    function addNewAddress() {
        var client_id = $('#task_customer_id').val();
        
        if (client_id == '' || client_id == 0) {
            alert('Please enter Customer');
        } else {
            $.ajax({
                url:'?m=clients&a=frmAddress&suppressHeaders=true',
                data: {client_id: client_id},
                type: 'POST',
                success:function(data){
                    $('#form_addnew_address').html(data);
                }
            });
            var id_='#form_addnew_address';
            
            $(id_).dialog({
                        resizable: false,
                        modal:true,
                        title: 'Address',
                        width: 600,
                        close: function(ev,ui){$(id_).dialog('destroy');} 
                });
            $(id_).dialog('open');
        }
    }
    
    function insertNewAddress()
    {
            var name_branch = $('#id_branch').val();
            var name_address1 = $('#id_address1').val();
            var name_address2 = $('#id_address2').val();
            var name_type = $('#name_type').val();
            var name_acc_code = $('#id_acc_code').val();
            var name_city = $('#id_ctiy').val();
            var name_country = $('#name_country').val();
            var name_province = $('#id_province').val();
            var name_zip_code = $('#id_zip_code').val();
            var name_phone1 = $('#id_phone1').val();
            var name_phone2 = $('#id_phone2').val();
            var name_mobile1 = $('#id_mobible1').val();
            var name_mobile2 = $('#id_mobible2').val();
            var name_email = $('#id_email1').val();
            var name_email2 = $('#id_email2').val();
            var name_fax = $('#id_fax').val();
            var name_website = $('#id_website').val();
            var name_notes = $('#id_notes').val();
            var client_id = $('#task_customer_id').val();
            var name_contractor = $('#id_contractor').val();
            var name_developer = $('#id_developer').val();
            var name_dlp = $('#id_dlp').val();
            var agent_company = $('#id_agent_company').val();
            var dlp_startdate = $('#dlp_date_value').val();
            var billing_preference = $('#id_billing_preference').val();

            $.post('?m=clients&a=add_address&suppressHeaders=true', 
                {
                    client_id: client_id,
                    name_branch: name_branch,
                    name_address1: name_address1,
                    name_address2: name_address2,
                    name_type: name_type,
                    name_acc_code: name_acc_code,
                    name_city: name_city,
                    name_country: name_country,
                    name_province: name_province,
                    name_zip_code: name_zip_code,
                    name_phone1: name_phone1,
                    name_phone2: name_phone2,
                    name_mobile1: name_mobile1,
                    name_mobile2: name_mobile2,
                    name_email: name_email,
                    name_email2: name_email2,
                    name_fax: name_fax,
                    name_website: name_website,
                    name_notes: name_notes,
                    name_contractor: name_contractor,
                    name_developer: name_developer,
                    name_dlp: name_dlp,
                    name_dlp_startdate: dlp_startdate,
                    name_agent_company: agent_company,
                    name_billing_preference: billing_preference,
                    act: 'addNewAddress'
                }, 
                function(data) {
                    if (data.status == 'success') {
                        closeDialogI('form_addnew_address');
                        load_address_dropdown();
                    } else {
                        alert(data.status);
                    }
                }, 'json'
            );
    }
    
    function insertClientNew() {
        var company_name = $('#id_family').val();
        var rcb_name = $('#id_given').val();
        var company_title = $('#customer_title').val();

        if(company_name == '') {
            alert("<?php echo $AppUI->_('Client name is empty');?>");
            $('#id_family').focus();
            return false
        } else if(rcb_name == "") {
            alert("<?php echo $AppUI->_('NRIC/Registration is empty');?>");
            $('#id_given').focus();
            return false
        } else{
            $.post('?m=clients&a=add_new_client&suppressHeaders=true', 
                { 
                    act: 'addNewClient',
                    company_name: company_name,
                    rcb_name: rcb_name,
                    company_title: company_title
                }, 
                function(data) {
                    if (data.status == 'success') {
                        closeDialogI('add_clients_new');
                        $('#task_customer_id').append('<option value="'+data.value+'" selected="selected">'+data.view+'</option>');
                        $("#task_customer_id").trigger("liszt:updated");
                        load_address_dropdown();
                    } else {
                        alert(data.status);
                    }
                }, 'json'
            );
        }
    }
    
    function addNewContact() {
            var clientid = $('#task_customer_id').val();
            var address_id = $('#sales_location_id').val();

            if (clientid == '') {
                alert('Please enter Customer');
            } else {
                    $.ajax({
                        url:'?m=clients&a=frmNewContact&suppressHeaders=true',
                        data: {client_id: clientid, address_id: address_id},
                        type: 'POST',
                        success:function(data){
                            $('#frm_add_brand_new_contact').html(data);
                        }
                    });
                    popupAddNew('frm_add_brand_new_contact', 'New Account');
            }
    }
    
    function insertNewContact() // for adding new contact
	{
            // validate
            var last_name = $('#new_contact_last_name').val();
            var first_name = $('#new_contact_first_name').val();
            var phone = $('#new_contact_phone').val();
            var email = $('#new_contact_email').val();
            var mobile = $('#new_contact_mobile').val();
            var notes = $('#new_contact_notes').val();
            var id = $('#new_contact_id').val();
            var user_role = $('#user_role').val();
            var username = $('#user_username').val();
            var password = $('#user_password').val();
            var password_re = $('#user_password_re').val();
            var address_id = $('#address_contact_id').val();
            var client = $('#contact_client_id').val();
            var check = true;
            var msg = '';

            if ( last_name == '' || first_name == '' ) {
                msg = '<?php echo $AppUI->_('Please key in Family Name and Given Name.'); ?>';
                show_msg(msg, 'msg_contact', 'err', 'show');
                check = false;
            } else if (mobile != '' && isNaN(mobile) == true) {
                msg = '<?php echo $AppUI->_('Mobile must is number.'); ?>';
                show_msg(msg, 'msg_contact', 'err', 'show');
                check = false;
            } else if (phone != '' && isNaN(phone) == true) {
                msg = '<?php echo $AppUI->_('Phone must is number.'); ?>';
                show_msg(msg, 'msg_contact', 'err', 'show');
                check = false;
            } else if (username == '' || password == '' || password_re == '') {
                msg = '<?php echo $AppUI->_('Please enter Username, Password and Retype Password.'); ?>';
                show_msg(msg, 'msg_contact', 'err', 'show'); // 'err': kieu tin nhan bao loi, 'show': hien tu dau den cuoi
                check = false;
            } else if (password != password_re) {
                msg = '<?php echo $AppUI->_('Passwords have to match.'); ?>';
                show_msg(msg, 'msg_contact', 'err', 'show'); // 'err': kieu tin nhan bao loi, 'show': hien tu dau den cuoi
                check = false;
            } else if (password.length < 8 || (password.match(/[a-z]/g) == null || password.match(/[A-Z]/g) == null || password.match(/[0-9]/g) == null)) {
                msg = '<?php echo $AppUI->_('Password must contains at least 8 characters, with the combination of small letters (a-z), capital letters (A-Z) and digits (0-9).'); ?>';
                show_msg(msg, 'msg_contact', 'err', 'show'); // 'err': kieu tin nhan bao loi, 'show': hien tu dau den cuoi
                check = false;
            } else {
                if ($('#new_contact_notify').is(':checked') == true) {
                    if (email == '') {
                        msg = '<?php echo $AppUI->_('Please enter email address.'); ?>';
                        show_msg(msg, 'msg_contact', 'err', 'show'); // 'err': kieu tin nhan bao loi, 'show': hien tu dau den cuoi
                        check = false;
                    } else {
                        if (test_email(email) != false)
                            check = true;
                        else {
                            msg = 'Invalid E-mail ID';
                            show_msg(msg, 'msg_contact', 'err', 'show'); // 'err': kieu tin nhan bao loi, 'show': hien tu dau den cuoi
                            check = false;
                        }
                    }
                    var contact_notify = 'checked';
              } else {
                    if (email != '') {
                        if (test_email(email) != false)
                            check = true;
                        else {
                            msg = 'Invalid E-mail ID';
                            show_msg(msg, 'msg_contact', 'err', 'show'); // 'err': kieu tin nhan bao loi, 'show': hien tu dau den cuoi
                            check = false;
                        }
                    } else {
                        check = true;
                    }
              }
          }
        if (check == true) {
            $('#saveContact').val('loading...');
                $.post('?m=contacts&a=ajax_addedit_contact&suppressHeaders=true',
                    {
                        client_id: client,
                        address_id: address_id,
                        contact_last_name: last_name,
                        contact_first_name: first_name,
                        contact_phone: phone,
                        contact_email: email,
                        contact_mobile: mobile,
                        contact_notes: notes,
                        user_role: user_role,
                        contact_id: id,
                        user_username: username,
                        user_password: password,
                        contact_notify: contact_notify
                    },
                    function(data) {
                        if (data.status == 'success') {
                            closeDialogI('frm_add_brand_new_contact');
                            load_contacts();
                        } else {
                            alert(data.status);
                        }
                    }, 'json'
                );
            }
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
                return true;
        }
    function load_contacts() {
            if ($('#sales_location_id').val() != 0 && $('#sales_location_id').val() != '') {
                var address_arr = $('#sales_location_id').val(); 
                var mang_gia_tri = 'address_id='+address_arr;

                $('#task_contact_id').load('?m=abms_resource_calendar&a=do_process&c=_do_load_contact_byaddress&suppressHeaders=true', mang_gia_tri, function() {
                    $("#task_contact_id").trigger("liszt:updated");
                });
            }
    }
    
</script>
<style>
.detailRowLabelPopup {
	background-color: #f6f6f6;	
	border-bottom: 1px solid;
	border-color: #CBDAE6;
	color: #000000;
	width: 150px;
	text-align: right;
}
</style>
    <table border="0" cellpadding="4" cellspacing="0" class="std" width="100%">
        <?php
        if ($job_id > 0) {
            $job_team_parent = $JobManager->getJobTeamParent($job_id);
            if (count($job_team_parent) > 0) {
                $job_teams = $JobManager->getJobTeam(false, $job_team_parent[0]['job_parent']);
            }
            if (count($job_teams) > 0) {
                $del_id   = '<a class="icon-delete icon-all" onclick="choose_delete('.$job_id.'); return false;" href="#">Delete</a>';
            } else {
                $del_id   = '<a class="icon-delete icon-all" onclick="delete_task('.$job_id.'); return false;" href="#">Delete</a>';
            }
            
            $a_customer = '';
            $a_task = '';
            if (isset($company_id))
                $a_customer = '?m=clients&a=view&company_id='.$company_id.'&tab=0';
            else {
                $a_customer = '?m=clients';
            }

            if (isset($job_id) && isset($company_id))
                $a_task = '?m=jobs&a=view_details&job_id='.$job_id.'&client_id='.$company_id;
            else {
                $a_task = '?m=jobs';
            }
        ?>
        <tr>
            <td colspan="2" class="hilite detailRowField" align="center">
                <h1><a class="icon-text icon-all" onclick="showText(<?php echo $job_id;?>); return false;" href="#">Text</a>
                <?php echo $del_id;?></h1>
                <a href="<?php echo $a_customer;?>" >&nbsp Customer Details</a>&nbsp&nbsp-&nbsp&nbsp<a href="<?php echo $a_task;?>" target="_blank">Task Page</a>
            </td>
        </tr>
        <?php } else { ?>
        <tr>
            <td colspan="2" align="right">
                <input type="button" value="New Customer" class="ui-button ui-state-default ui-corner-all" onclick="popupAddClient('add_clients_new','Add New Client ');"/>&nbsp;
                <input type="button" value="New Address" class="ui-button ui-state-default ui-corner-all" onclick="addNewAddress();"/>&nbsp;
                <input type="button" value="New Contact" class="ui-button ui-state-default ui-corner-all" onclick="addNewContact();"/>&nbsp;
                <input type="button" value="New Equipment" class="ui-button ui-state-default ui-corner-all" onclick="loadFrmEquipment();">
            </td>
        </tr>
        <?php } ?>
        <tr>
                <td colspan="2" class="hilite detailRowField" align="center">
                    <b><?php echo $AppUI->_('* Customer');?>:</b> &nbsp;
                        <select class="text" id="task_customer_id" name="task_customer_id" style="width: 350px;" onchange="load_address_dropdown($(this).val());">
                            <option value=""> - Select a Customer -</option>
                                <?php
                                    if (sizeof($rows) > 0) {

                                        foreach ($rows as $row) {
                                            if (isset($company_id) && intval($company_id) == intval($row['company_id']))
                                                echo '<option value="'. $row['company_id'] .'" selected>'. stripcslashes($row['company_name']) .'</option>';
                                            else
                                                echo '<option value="'. $row['company_id'] .'">'. stripcslashes($row['company_name']) .'</option>';
                                        }
                                    }
                                ?>
                        </select>
                </td>
        </tr>
        <tr>
                <td width="100%" class="hilite detailRowField" colspan="2" align="center">
                        <input type="radio" name="address_types" id="sales_task_all" value="all" checked/> All <input type="radio" name="address_types" id="sales_task_contract" value="contract"/> Contract <input type="radio" name="address_types" id="sales_task_call-in" value="call-in"/> Call-in
                        <input type="hidden" id="address_type_values" value="all"/>
                </td>
        </tr>
	<tr valign="top">
		<td width="50%" align="left">
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td align="right" class="detailRowLabelPopup"><?php echo $AppUI->_('* Location');?>:</td>
					<td class="hilite detailRowField" width="80%">
						<select class="text" name="location_id[]" id="sales_location_id" style="height: 60px; width: 250px;" multiple="multiple" onchange="load_contact_address_task($(this).val());">
						<?php
                                                    echo $option_location;
                                                ?>
                                                </select>
					</td>
				</tr>
                                <tr>
                                        <td align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('Contacts');?>:</td>
                                        <td class="hilite detailRowField">
                                                <select class="text" name="contact_id[]" id="task_contact_id" style="height: 60px; width: 250px;" multiple="multiple">
						<?php
                                                    echo $option_contact;
                                                ?>
                                                </select>
                                        </td>
                                </tr>
                                <tr>
                                        <td align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('Customer Note');?>:</td>
                                        <td class="hilite detailRowField">
						<TEXTAREA id="sales_customer_note" name="customer_note" style="height:60px; width: 80%;" class="text" cols="40" rows="15" readonly="readonly"><?php echo $customer_notes;?></TEXTAREA>
					</td>
                                </tr>
                                <tr>
                                        <td align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('Job Note');?>:</td>
                                        <td class="hilite detailRowField">
                                            <TEXTAREA id="sales_note" name="note" style="height:60px; width: 80%;" class="text" cols="20" rows="15"><?php echo $notes;?></TEXTAREA>
                                        </td>
                                </tr>
                                <?php
                                    if (in_array('partner', $user_roles) || in_array('coordinator', $user_roles)) {
                                ?>
                                <tr>
                                        <td align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('* Team');?>:</td>
                                        <td class="hilite detailRowField">
                                                <select name="team_id" id="sales_team_id" class="text" multiple="multiple" style="height: 60px; width: 250px;" onchange="load_members($(this).val());">
                                                    <?php
                                                    require_once (DP_BASE_DIR."/modules/team/CTeamManager.php");
                                                    $teamManager = new CTeamManager();
                                                    $team_arr = $teamManager->list_team();
                                                    $member_select = '';
                                                    if (count($team_arr) > 0) {
                                                        foreach ($team_arr as $team) {
                                                            if (isset($team_id) && intval($team_id) == intval($team['team_id']))
                                                                echo '<option value="'. $team['team_id'] .'" selected>'. stripcslashes($team['team_name']) .'</option>';
                                                            else 
                                                                echo '<option value="'. $team['team_id'] .'">'. stripcslashes($team['team_name']) .'</option>';
                                                            
                                                            $member_select .= '<optgroup label="'.$team['team_name'].'">';
                                                            $member_team = $teamManager->list_team_default($team['team_id']);
                                                            if (count($member_team) > 0) {
                                                                foreach ($member_team as $mem) {
                                                                    if (in_array($mem['user_id'], $ass_job_arr) == true)
                                                                        $member_select .= '<option value="'. $mem['user_id'] .'" selected="selected">'. stripcslashes($mem['user_username']) .'</option>';
                                                                    else 
                                                                        $member_select .= '<option value="'. $mem['user_id'] .'">'. stripcslashes($mem['user_username']) .'</option>';
                                                                }
                                                            }
                                                            $member_select .= '</optgroup>';
                                                        }
                                                    }
                                                    
                                                    ?>
                                                </select>
                                        </td>
                                </tr>
                                <?php
                                    }
                                ?>
                                <tr>
                                        <td align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('* Date');?>:</td>
                                        <td class="hilite detailRowField">
                                                <input type="text" readonly="readonly" class="text" size="10" id="sales_startdate_display" value="<?php echo $start_date;?>">
                                                <input type="hidden" size="10" id="sales_startdate_value" name="startdate_value" value="<?php echo $date_value;?>">
                                        </td>
                                </tr>
                                <tr>
                                        <td align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('* Time');?>:</td>
                                        <td class="hilite detailRowField">
                                                <select class="text" name="start_time" id="sales_start_time"><?php echo $option_time_blog; ?></select>
                                        </td>
                                </tr>
                                <tr>
                                        <td align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('* Duration');?>:</td>
                                        <td class="hilite detailRowField">
                                                <input type="text" id="sales_duration" name="duration" value="<?php echo $duration;?>" class="text" size="10"/>
                                        </td>
                                </tr>

			</table>
		</td>
                <td width="50%" align="left">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
					<td  align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('Service');?>:</td>
					<td class="hilite detailRowField">
						<select class="text" id="sales_service" name="service" style="width: 80%;">
                                                    <option value=""> - Select a Service - </option>
							<?php
                                                        if (count($services) > 0) {
                                                            foreach ($services as $service) {
                                                                if (isset($service_id) && intval($service_id) == intval($service['config_id']))
                                                                    echo '<option value="'. $service['config_id'] .'" selected>'. $service['config_name'] .'</option>';
                                                                else 
                                                                    echo '<option value="'. $service['config_id'] .'">'. $service['config_name'] .'</option>';
                                                            }
                                                        }
                                                        ?>
						</select>
					</td>
				</tr>
                                <?php
                                if (in_array('technician', $user_roles) == false && in_array('customer', $user_roles) == false && in_array('agent', $user_roles) == false && in_array('tenant', $user_roles) == false) {
                                ?>
                                <tr>
                                        <td align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('Service Order Template');?>:</td>
                                        <td class="hilite detailRowField">
                                            <select name="service_order_temp" id="sales_service_order_temp" class="text" multiple="multiple" style="height: 60px; width: 250px;">
                                                <?php 
                                                $template_arr = $serviceOrder->getServiceOrderTemplate();
                                                if (count($template_arr) > 0){
                                                    foreach ($template_arr as $template) {
                                                        if (isset($template_id_arr) && in_array(intval($template['service_order_template_id']), $template_id_arr))
                                                            echo '<option value="'. $template['service_order_template_id'] .'" selected="selected">'. $template['service_order_template_name'] .'</option>';
                                                        else
                                                            echo '<option value="'. $template['service_order_template_id'] .'">'. $template['service_order_template_name'] .'</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </td>
                                </tr>
                                <tr>
                                        <td align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('Members');?>:</td>
                                        <td class="hilite detailRowField">
                                            <select name="member_id" id="sales_member_id" class="text" multiple="multiple" style="height: 60px; width: 250px;">
                                                <?php echo $member_select;?>
                                            </select>
                                        </td>
                                </tr>
                                <?php }?>
                                <tr>
                                        <td align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('Equipments');?>:</td>
                                        <td class="hilite detailRowField">
                                            <select name="equipments" id="sales_equipments" class="text" multiple="multiple" style="height: 60px; width: 250px;">
                                                <?php
                                                    echo $option_equipment;
                                                ?>
                                            </select>
                                        </td>
                                </tr>
				<tr>
					<td  align="right" nowrap="nowrap" class="detailRowLabelPopup"><?php echo $AppUI->_('Status');?>:</td>
					<td class="hilite detailRowField">
						<select class="text" id="sales_status" name="status" style="width: 80%;">
							<?php
	 							if(count($job_status)>0)
	 							{
                                                                    if (in_array('customer', $user_roles) || in_array('agent', $user_roles) || in_array('tenant', $user_roles)) {
                                                                        foreach ($job_status as $status) {
                                                                            if (isset($status_id) && $status['config_id'] == $status_id)
                                                                                echo '<option value="'.$status['config_id'].'" selected>'.$status['config_name'].'</option>';
                                                                            if ($status['config_name'] == 'Pending') 
                                                                                echo '<option value="'.$status['config_id'].'">'.$status['config_name'].'</option>';
                                                                        }
                                                                    } else {
	 								foreach($job_status as $status)
	 								{
                                                                            if (isset($status_id) && $status['config_id'] == $status_id)
                                                                                echo '<option value="'.$status['config_id'].'" selected>'.$status['config_name'].'</option>';
                                                                            else 
                                                                                echo '<option value="'.$status['config_id'].'">'.$status['config_name'].'</option>';
									}
                                                                    }
								}
	
							?>
						</select>
					</td>
				</tr>
                        </table>
                </td>
	</tr>
        <tr>
            <td align="center" colspan="2">
                    <input type="hidden" id="job_id" value="<?php echo $job_id;?>"/>
                    <button onclick="add_new_tasks();" id="saveJob" class="ui-button ui-state-default ui-corner-all" type="submit" value="Save"><?php echo $AppUI->_('Save');?></button>
                    <button onclick="closeDialogPopup('sales_task_popup-editTask');" class="ui-button ui-state-default ui-corner-all" type="submit" value="Cancel"><?php echo $AppUI->_('Cancel');?></button>
            </td>
        </tr>
        <tr><td colspan="2"><hr/></td></tr>
        <?php
        if (in_array('partner', $user_roles) || in_array('coordinator', $user_roles)) {
        ?>
        <tr>
            <td align="center" colspan="2">
                <input type="checkbox" value="" id="choice_field_print"/><?php echo $AppUI->_('Fields to show for Printing');?>&nbsp&nbsp- &nbsp&nbsp<a href="#"  onclick="print_show(); return false;">&nbsp Show</a> / <a href="#" onclick="print_hide(); return false;">&nbsp Hide</a>
                
            </td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                <div id="sales_field_print" style="display: none;">
                    <?php
                    $field_checked = array();
                    if (isset($job_id) && $job_id > 0) {
                        $field_jobs = $JobManager->get_job_field(false, $job_id);
                        if (count($field_jobs) > 0) {
                            foreach ($field_jobs as $field_job) {
                                $field_checked[] = $field_job['field_print'];
                            }
                        }
                    } else {
                        $field_checked = array('company', 'customer_note', 'address', 'service', 'contact', 'note', 'phone_1', 'mobile_1');
                    }
                    ?>
                    <table width="40%">
                        <tr>
                            <td><input type="checkbox" value="company" name="field_choose" <?php if(in_array('company', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Company');?></td>
                            <td><input type="checkbox" value="phone_1" name="field_choose" <?php if(in_array('phone_1', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Phone 1');?></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" value="customer_note" name="field_choose" <?php if(in_array('customer_note', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Customer Note');?></td>
                            <td><input type="checkbox" value="phone_2" name="field_choose" <?php if(in_array('phone_2', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Phone 2');?></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" value="address" name="field_choose" <?php if(in_array('address', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Address');?></td>
                            <td><input type="checkbox" value="mobile_1" name="field_choose" <?php if(in_array('mobile_1', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Mobile 1');?></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" value="service" name="field_choose" <?php if(in_array('service', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Service');?></td>
                            <td><input type="checkbox" value="mobile_2" name="field_choose" <?php if(in_array('mobile_2', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Mobile 2');?></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" value="contact" name="field_choose" <?php if(in_array('contact', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Contact');?></td>
                            <td><input type="checkbox" value="email_1" name="field_choose" <?php if(in_array('email_1', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Email 1');?></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" value="note" name="field_choose" <?php if(in_array('note', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Note');?></td>
                            <td><input type="checkbox" value="email_2" name="field_choose" <?php if(in_array('email_2', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Email 2');?></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" value="equipment" name="field_choose" <?php if(in_array('equipment', $field_checked)) echo 'checked';?>/><?php echo $AppUI->_('Equipments');?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="2">
                                    <button onclick="add_new_tasks();" id="saveJob" class="ui-button ui-state-default ui-corner-all" type="submit" value="Save"><?php echo $AppUI->_('Save');?></button>
                                    <button onclick="closeDialogPopup('sales_task_popup-editTask');" class="ui-button ui-state-default ui-corner-all" type="submit" value="Cancel"><?php echo $AppUI->_('Cancel');?></button>
                            </td>
                        </tr>
                    </table>
                    
                </div>
            </td>
        </tr>
        
        <?php }?>
    </table>

<div id="frm_newEquipment" style="display:none"></div>
<div id="frm_add_brand_new_contact" style="display: none;" align="center">
 <?php
 $customer_titles = dPgetSysVal('CustomerTitle');
 $array_types1 = $cconfig->getRecordConfigByList('typeClients');
 
 ?>
 <div id="add_clients_new" style="display:none;" align="center">
	<form action = "index.php?m=clients" method="POST" name="add_new_form_clients">
            <input type="hidden" name="show_all" id="show_all" value="1"/>
		<div style="width:95%;" >
			<div style="float:left;width:200px; text-align: left">Name</div>
			<div align="left"> <input type="text" name="name_family" id="id_family" class="text"></div>
		</div>
                <div style="width:95%;" >
			<div style="float:left;width:250px; text-align: left">Title</div>
			<div align="left"><select name="customer_title" id="customer_title" class="text">
                                <option value=""> </option>
                                <?php
                                foreach($customer_titles as $key => $value){
                                    echo '<option value="' . $key . '">' . $value . '</option>';
                                }
                                ?>
                            </select>
                        </div>
		</div>
                <div style="width:95%;" >
			<div style="float:left;width:200px; text-align: left">Type</div>
			<div align="left"><select name="customer_type" id="customer_type" class="text">
                                <?php
                                foreach($array_types1 as $c_type){
                                    echo '<option value="' . $c_type['config_id'] . '">' . $c_type['config_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
		</div>
		<div style="width:95%;">
			<div style=" clear:both;float:left;width:200px; text-align: left">NRIC/Registration</div>
			<div style="" align="left"><input type="text" name="name_given" id="id_given" class="text"></div>
		</div>
		<div style="width:95%; text-align: center">
			<input type="button" name="name_add" id="id_add_new" value="Save" class="ui-button ui-state-default ui-corner-all" style="" onclick = "insertClientNew();">
			&nbsp;<input type="button" name="name_close" id="id_close" value="Cancel" class="ui-button ui-state-default ui-corner-all" style="" onclick="closeDialogI('add_clients_new');">
		</div>
	</form>
</div>
 <div id="form_addnew_address" style="display:none;" align="center"></div>
                        

    