<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

require_once(DP_BASE_DIR."/modules/system/roles/roles.class.php");

global $AppUI;

$address_id = dPgetParam($_POST, 'address_id', 0);
if (is_array($address_id))
    $address_id = $address_id[0];
$client_id = dPgetParam($_POST, 'client_id', 0);
$status = dPgetParam($_POST, 'status', "");
$att_status = dPgetParam($_POST, 'att_status','');

$CRole = new CRole();
$role_agent = $CRole->getRolesByValue('agent');
$role_customer = $CRole->getRolesByValue('customer');
$role_tenant = $CRole->getRolesByValue('tenant');
$role_type = array( $role_customer['id'] => $role_customer['name'], $role_agent['id'] => $role_agent['name'], $role_tenant['id'] => $role_tenant['name'] );

// Get title dropdown
$contacts_title = array('' => '');
$contacts_title += dPgetSysVal('ContactsTitle');
$contacts_title_dropdown = arraySelect($contacts_title, 'pop_contacts_title_id', 'id="pop_contacts_title_id" class="text" size="1"', 0, true);
?>
<script>

    function creatAccount(for_change) {
        var given_name = document.getElementById("new_contact_first_name").value;
        var family_name = document.getElementById("new_contact_last_name").value;
        var msg = '';

        if ($('#new_agent_acc_auto_create').is(':checked') == true) { // Neu checkbox da duoc check thi moi thuc hien
            if (family_name == '' || given_name == '') {
                msg = '<?php echo $AppUI->_('Please enter family name and given name !'); ?>';
                show_msg(msg, 'msg_contact', 'err', 'show');
            } else {
                getUserName(family_name.toLowerCase(), given_name.toLowerCase(), for_change);
            }
        } else {
            $('#user_username').val('');
            $('#attemp_creat').val(0);
            $('#attemp_creat_change').val(1);
            $('#change_user').hide();
        }
    }
    function getUserName(family_name, given_name, for_change) {
        if (for_change == null) {
            var count_creat = parseInt(document.getElementById('attemp_creat').value);
        } else {
            var count_creat = parseInt(document.getElementById('attemp_creat_change').value);
        }

        /*
        * ex: family_name: "do xuan", given_name: "tinh", dob: 1988-09-05
        */

        var user_name = '';
        var k,j,i,h;

        if (count_creat == 0 || count_creat == 1) {
            given_name = given_name.split(" ");
            family_name = family_name.split(" ");

            for (k = 0; k < given_name.length; k++) { // loai bo tat ca ky tu ' '
                if (given_name[k] != ' ')
                    user_name += given_name[k];
            }
            for (j = 0; j < family_name.length; j++) {
                if (family_name[j] != ' ')
                    user_name += family_name[j]; // user_name = tinhdoxuan
            }
            if (count_creat == 0)
                user_name += '';
            if (count_creat == 1) { // user_name = tinhdoxuan2012
            user_name += (new Date().getFullYear()); 
            }
        } else if (count_creat == 2 || count_creat == 3 || count_creat == 4 || count_creat == 5) {
            var user_name_0 = '';
            given_name = given_name.split(" ");

            for (h = 0; h < given_name.length; h++) { // loai bo tat ca ky tu ' '
                if (given_name[h] != ' ')
                    user_name_0 += given_name[h];
            }
            var family_name_arr = family_name.split(" ");
            if (family_name_arr.length > 1) {
                var user_name_1 = '';
                for (i = 0; i < family_name_arr.length; i++) {
                    if (family_name_arr[i].length != 0) {
                        var family_name_0 = family_name_arr[i].split("");
                        user_name_1 += family_name_0[0];
                    }
                }
            } else {
                if (family_name_arr[0].length != 0) {
                    var family_name_00 = family_name_arr[0].split("");
                    user_name_1 = family_name_00[0];
                }
            }

            if (count_creat == 2) // tinhdx
                user_name += user_name_0 + user_name_1;
            if (count_creat == 3) // tinh.dx
                user_name += user_name_0 + '.' + user_name_1;
            if (count_creat == 4) // dx.tinh
                user_name += user_name_1 + '.' + user_name_0;
            if (count_creat == 5) // tinh2012 (nam hien hanh)
                user_name += user_name_0 + (new Date().getFullYear());
        }

        if (user_name != '') {
            getUserNameMore(family_name, given_name, count_creat, user_name, for_change);
        }
    }
    
    function getUserNameMore(family_name, given_name, count_creat, user_name, for_change, count_loop) {
        var count_creat_max = 5;
        $('#user_username').css("background", "url('images/indicator.gif') right center no-repeat");
        $.get('?m=clients&a=do_process_account&suppressHeaders=true&action=check_username', { user_name: user_name }, function(data) {
                if (data == "success") {
                    if (for_change == null) {
                        if (count_creat == count_creat_max)
                            $('#attemp_creat').val(0);
                        else
                            $('#attemp_creat').val(count_creat+1);
                    } else {
                        if (count_creat == count_creat_max)
                            $('#attemp_creat_change').val(1);
                        else
                            $('#attemp_creat_change').val(count_creat+1);
                    }
                        $('#user_username').css("background", "none");
                        $('#user_username').val(user_name);
                        $('#change_user').show();
                } else {
                    if (for_change == null) {
                        if (count_creat == count_creat_max)
                            $('#attemp_creat').val(0);
                        else
                            $('#attemp_creat').val((count_creat++));
                    } else {
                        if (count_creat == count_creat_max)
                            $('#attemp_creat_change').val(1);
                        else
                            $('#attemp_creat_change').val((count_creat++));
                    }
                    if (count_loop == null) count_loop = 0;
                    if (count_loop < 10) { // Viec nay lam cho vong lap chi chay den 1000 quet het cac gia tri giong nhau, neu khong co se ngung lap
                        var stt = Math.floor(Math.random()*1000);
                        if (stt < 10) stt = '00'+stt;
                        if (stt < 100) stt = '0'+stt;
                        user_name = data + stt; // Xac dinh neu co 1 user nao giong ten se duoc lay ngau nhien tu 1->1000 cho vao chuoi dang sau
                        getUserNameMore(family_name, given_name, count_creat++, user_name, for_change, count_loop+1); // data la username
                    } 
                }
        });
    }

    function getRandomNum(lbound, ubound) {
        return (Math.floor(Math.random() * (ubound - lbound)) + lbound);
    }

    function getRandomChar() {
        var numberChars = "0123456789";
        var lowerChars = "abcdefghijklmnopqrstuvwxyz";
        var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        var charSet = numberChars;
        charSet += lowerChars;
        charSet += upperChars;
        return charSet.charAt(getRandomNum(0, charSet.length));
    }

    function getPassword(attemp) {
        var numberChars = "0123456789";
        var lowerChars = "abcdefghijklmnopqrstuvwxyz";
        var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        var length = 8;
        var rc = "";
        var number = false; var lower = false; var upper = false;
        for (var idx = 0; idx < length; ++idx) {
            //rc = rc + getRandomChar();
            var rc_1 = getRandomChar();
            if (numberChars.indexOf(rc_1) != -1)
                number = true;
            if (lowerChars.indexOf(rc_1) != -1)
                lower = true;
            if (upperChars.indexOf(rc_1) != -1)
                upper = true;
            rc += rc_1;
        }
        if (upper && lower && number) {
            $('#user_password').css("background", "url('images/indicator.gif') right center no-repeat");
            $.get('?m=clients&a=do_process_account&suppressHeaders=true&action=check_password', { password: rc }, function(data) {
                if (data == "success") {
                    document.getElementById('user_password').value = rc;
                    document.getElementById('user_password_re').value = rc;
                    document.getElementById('show_creat_pw').innerHTML = rc;
                    $('#user_password').css("background", "none");
                } else if (data == "Failure") {
                    if (attemp < 1)
                        getPassword(attemp+1);
                    else {
                        $('#pass_fail').css("color", "red");
                        show_msg('<br>&nbsp;&nbsp;No result found.', 'pass_fail', 'none', 'hide');
                    }
                }
            });
        } else {
            if (attemp < 1)
                getPassword(attemp+1);
            else {
                $('#pass_fail').css("color", "red");
                show_msg('<br>&nbsp;&nbsp;No result found.', 'pass_fail', 'none', 'hide');
            }
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
            var fax = $('#new_contact_fax').val();
            var notes = $('#new_contact_notes').val();
            var id = $('#new_contact_id').val();
            var user_role = $('#user_role').val();
            var username = $('#user_username').val();
            var password = $('#user_password').val();
            var password_re = $('#user_password_re').val();
            var address_id = $('#address_contact_id').val();
            var title = $('#pop_contacts_title_id').val();
            var status = $('#status').val();
            var att_status = $('#att_status').val();
            var check = true;
            var msg = '';
            //var client = <?php echo ($_GET['company_id'] ? $_GET['company_id'] : 0)?>;
            var client_id = $('#contact_client_id').val();
            //alert(client_id+","+client);

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
                        client_id: client_id,
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
                        contact_notify: contact_notify,
                        title:title,
                        contact_fax:fax,
                    },
                    function(data) {
                        if (data.status == 'success') {
                            if(status == "invoice"){
                                if(att_status=="attention")
                                   $('#inv_attention_id').load('?m=sales&a=vw_invoice&c=_get_attention_select&suppressHeaders=true', { customer_id: client_id,contact_id:data.contact_id});
                                else
                                    $('#inv_contact_id').load('?m=sales&a=vw_invoice&c=_get_contact_jobLocation&suppressHeaders=true&job_location_id='+address_id+'&contact_id='+data.contact_id);
                            }
                            else{
                                if(att_status == "attention")
                                    $('#attention_id').load('?m=sales&a=vw_quotation&c=_get_attention_select&suppressHeaders=true', { customer_id: client_id,contact_id:data.contact_id});
                                else
                                    $('#contact_id').load('?m=sales&a=vw_quotation&c=_get_contact_jobLocation&suppressHeaders=true&job_location_id='+address_id+'&contact_id='+data.contact_id);
                            }
                             closeDialogI('div-popup');
                        } else {
                            alert(data.status);
                        }
                    }, 'json'
                );
            }
	}
	function closeDialogI(id){
		var id_='#'+id;
	
		$(id_).dialog('close');
	}

</script>
<div style="width: 85%; margin: auto"  >
<div style="width: 95%; display: none" id="msg_contact" ></div>
    <table border="0" width="100%" cellspacing="0" cellpadding="8" class="tbl">
        <tr width="100%">
            <td>Family Name:*</td>
            <td><input type="text" class="text" name="contact_last_name" id="new_contact_last_name"/></td>
        </tr>
        <tr>
            <td>Given Name:*</td>
            <td><input type="text" class="text" name="contact_first_name" id="new_contact_first_name"/></td>
        </tr>
        <tr>
            <td>User Role:*</td>
            <td>
                <select class="text" size="1" name="user_role" id="user_role">
                    <option value="<?php echo $role_customer['id'];?>"><?php echo $role_customer['name'];?></option>
                    <option value="<?php echo $role_agent['id'];?>"><?php echo $role_agent['name'];?></option>
                    <option value="<?php echo $role_tenant['id'];?>"><?php echo $role_tenant['name'];?></option>
                </select>
                <input type="hidden" id="role_agent" name="role_agent" value="<?php echo $role_agent['id'];?>" />
            </td>
        </tr>
        <tr>
            <td>Title: </td>
            <td><?php echo $contacts_title_dropdown;  ?></td>
        </tr>
        <tr>
            <td>Office Phone:</td>
            <td><input type="text" class="text" name="contact_phone" id="new_contact_phone"/></td>
        </tr>
        <tr>
            <td>Email:</td>
            <td><input type="text" class="text" name="contact_email" id="new_contact_email"/></td>
        </tr>
        <tr>
            <td>Mobile:</td>
            <td><input type="text" class="text" name="contact_mobile" id="new_contact_mobile"/></td>
        </tr>
        <tr>
            <td>Fax:</td>
            <td><input type="text" class="text" name="contact_fax" id="new_contact_fax"/></td>
        </tr>
        <tr>
            <td>Contact Notes:</td>
            <td><textarea class="text" style="height: 50px" name="contact_notes" id="new_contact_notes" ></textarea></td>
        </tr>
        <tr>
            <td colspan="2"><h2>Login Account</h2></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="checkbox" name="new_contact_notify" id="new_contact_notify"/><label for="new_contact_notify"><?php echo $AppUI->_('Notify Contact'); ?></label>
                &nbsp;<input type="checkbox" name="new_agent_acc_auto_create" id="new_agent_acc_auto_create" onchange="creatAccount();"/>
                        <label for="new_agent_acc_auto_create"><?php echo $AppUI->_('Auto Generate Login Account'); ?></label>
            </td>
        </tr>
        <tr>
            <td width="150px"><?php echo $AppUI->_('Username: *');?></td>
            <td><input type="text" name="user_username" id="user_username" size="20" class="text"/>&nbsp;<a onclick="creatAccount(0); return false;" id="change_user" href="#" class="dashed" style=" display: none"><?php echo $AppUI->_('Change'); ?></a></td>
        </tr>
        <tr>
            <td width="150px"><?php echo $AppUI->_('Password: *');?></td>
            <td><input type="password" name="user_password" id="user_password" size="20" class="text"/>&nbsp;<a onclick="getPassword(0); return false;" href="#" class="dashed"><?php echo $AppUI->_('Create'); ?></a>:&nbsp; <span id="show_creat_pw"></span>
            <span id="pass_fail"></span></td>
        </tr>
        <tr>
            <td width="150px"><?php echo $AppUI->_('Retype Password: *');?></td>
            <td><input type="password" name="user_password_re" id="user_password_re" size="20" class="text"/></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input type="hidden" name="contact_id" id="new_contact_id" value="0"/>
                <input type="hidden" name="address_contact_id" id="address_contact_id" value="<?php echo $address_id;?>"/>
                <input type="hidden" name="contact_client_id" id="contact_client_id" value="<?php echo $client_id;?>"/>
                <input type="hidden" name="status" id="status" value="<?php echo $status; ?>" />
                <input type="hidden" name="att_status" id="att_status" value="<?php echo $att_status; ?>" />
                <input type="submit" value="Save" id="saveContact" class="ui-button ui-state-default ui-corner-all" onclick="insertNewContact();">
		<input type="button" value="Close" class="ui-button ui-state-default ui-corner-all"  onclick="closeDialogI('div-popup');">
                <input type="hidden" id="attemp_creat" value="0"/>
                <input type="hidden" id="attemp_creat_change" value="1"/>
                <label id="load_add"/>
            </td>
        </tr>
    </table>
</div>
