<?php
if (!defined('DP_BASE_DIR')){
  die('You should not access this file directly.');
}

//echo "<script src='js/chosen.jquery.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' type='text/css' href='style/default/chosen.css' />";

require_once (DP_BASE_DIR. '/modules/system/cconfig.class.php');
require_once (DP_BASE_DIR. '/modules/clients/company_manager.class.php');

$CConfig = new CConfigNew();
$companyManger = new CompanyManager();

$atypes= dPgetSysVal('AddressType');
 
$array_countries5 =$CConfig->getRecordConfigByList('countriesList');
$list_countries5 = array();
if(count($array_countries5)>0)
{
        foreach($array_countries5 as $array_country5)
        {
                if($array_country5['config_status']!=0)
                {
                        $list_countries5[$array_country5['config_id']] = $array_country5['config_name'];
                }
        }
}

$active_id = $CConfig->getRecordConfigByName('active',1);
$companies = $companyManger->getCompanies(false, $active_id);
?>
<script language="javascript">
    var js_display_date_format="<?php echo (dPgetConfig('js_display_date_format')? dPgetConfig('js_display_date_format') : dPgetConfig('js_display_date_format_default'))?>";
    var js_input_date_format = "<?php echo (dPgetConfig('js_input_date_format') ? dPgetConfig('js_input_date_format') : dPgetConfig('js_input_date_format_default'))?>";
    $(document).ready(function(){
        bindDatePicker('sales_dlp_date');
        
        $('#sales_id_contractor').chosen();
        $('#sales_id_developer').chosen();
        var config = {
                '#sales_id_agent_company'  : {}
        }
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }
    });
    
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
	function insertNewAddress()
	{
                var name_branch = $('#sales_id_branch').val();
                var name_address1 = $('#sales_id_address1').val();
                var name_address2 = $('#sales_id_address2').val();
                var name_type = $('#sales_name_type').val();
                var name_acc_code = $('#sales_id_acc_code').val();
                var name_city = $('#sales_id_ctiy').val();
                var name_country = $('#sales_name_country').val();
                var name_province = $('#sales_id_province').val();
                var name_zip_code = $('#sales_id_zip_code').val();
                var name_phone1 = $('#sales_id_phone1').val();
                var name_phone2 = $('#sales_id_phone2').val();
                var name_mobile1 = $('#sales_id_mobible1').val();
                var name_mobile2 = $('#sales_id_mobible2').val();
                var name_email = $('#sales_id_email1').val();
                var name_email2 = $('#sales_id_email2').val();
                var name_fax = $('#sales_id_fax').val();
                var name_website = $('#sales_id_website').val();
                var name_notes = $('#sales_id_notes').val();
                var name_contractor = $('#sales_id_contractor').val();
                var name_developer = $('#sales_id_developer').val();
                var name_dlp = $('#sales_id_dlp').val();
                var dlp_startdate = $('#sales_dlp_date_value').val();
                var agent_company = $('#sales_id_agent_company').val();
                var client_id = $('#sales_address_company_id').val();
                var status = $('#sales_status').val();
//                alert(client_id);
                
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
                        act: 'addNewAddress'
                    }, 
                    function(data) {
                        if (data.status == 'success') {
                            closeDialogI('div-popup');
                            if(status=="invoice"){
                                //$('#inv_address_id').load('?m=sales&a=vw_invoice&c=_get_address_select&suppressHeaders=true', { customer_id: client_id});
                                $('#sel_address').load('?m=sales&a=vw_invoice&c=_get_address_select&suppressHeaders=true', { customer_id: client_id});
                                //$('#inv_job_location_id').load('?m=sales&a=vw_invoice&c=_get_job_address_select&suppressHeaders=true', { customer_id: client_id});
                                $('#sel_job_location').load('?m=sales&a=vw_invoice&c=_get_job_address_select&suppressHeaders=true', { customer_id: client_id});
                            }
                            else{
                                $('#quo_sel_address').load('?m=sales&a=vw_quotation&c=_get_address_select&suppressHeaders=true', { customer_id: client_id});
                                $('#quo_sel_location').load('?m=sales&a=vw_quotation&c=_get_job_location_select&suppressHeaders=true', { customer_id: client_id});
                            }
                        } else {
                            alert(data.status);
                        }
                    }, 'json'
                );
	}
	function closeDialogI(id){
		var id_='#'+id;
	
		$(id_).dialog('close');
	}
</script>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Branch </div>
            <div style=""><input type="text" name="name_branch" id="sales_id_branch" class="text"></div>
    </div>		
    <div style="width:95%;" >
            <div style="clear:both;float:left;width:30%;" align="left">Street Addresses 1</div>
            <div > <input type="text" name="name_address1" id="sales_id_address1" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Street Addresses 2 </div>
            <div style=""><input type="text" name="name_address2" id="sales_id_address2" class="text"></div>
    </div>
    <div style="width:95%">
            <div style=" clear: both;float: left;width: 30%" align="left">Type Address </div>
            <div style="">
                <select name="name_type" id="sales_name_type" style="width:30%" class="text">
                    <?php
                    foreach ($atypes as $keytype=>$list_type) { ?>
                        <option value="<?php echo $keytype?>"><?php echo $list_type;?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
    </div>
<div style="width:95%">
    <div style="clear:both;float: left;width: 30%" align="left">Account code </div>
    <div style=""><input type="text" name="name_acc_code" id="sales_id_acc_code" class="text"/></div>
</div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">City </div>
            <div style=""><input type="text" name="name_city"  id="sales_id_ctiy" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;"align="left" >Country </div>			
            <div style="" align="center"  >
            <?php			
            //$countries = dPgetSysVal('Countries');
            ?>
                    <select  name = "name_country" id="sales_name_country" style="width:31%;" class = "text">		
                    <?php 
                    foreach($list_countries5 as $key=>$list_country){ ?>
                            <option value = "<?php echo $key?>"> <?php echo $list_country;?></option>				

                    <?php
                    }?>					
                    </select>
            </div>	
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">State/Province </div>
            <div style=""><input type="text" name="name_province" id="sales_id_province" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Postal/Zip</div>
            <div style=""><input type="text" name="name_zip_code" id="sales_id_zip_code" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Phone 1</div>
            <div style=""><input type="text" name="name_phone1" id="sales_id_phone1" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Phone 2</div>
            <div style=""><input type="text" name="name_phone2" id="sales_id_phone2" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Mobile 1</div>
            <div style=""><input type="text" name="name_mobile1" id="sales_id_mobible1" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Mobile 2</div>
            <div style=""><input type="text" name="name_mobile2" id="sales_id_mobible2" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Email 1</div>
            <div style=""><input type="text" name="name_email" id="sales_id_email1" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Email 2</div>
            <div style=""><input type="text" name="name_email2" id="sales_id_email2" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Fax</div>
            <div style=""><input type="text" name="name_fax" id="sales_id_fax" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Website</div>
            <div style=""><input type="text" name="name_website" id="sales_id_website" class="text"></div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Agent Company</div>
            <div style="">
                <select name="name_agent_company" id="sales_id_agent_company" style="width: 41%" class="text" multiple="multiple">
                    <option value=""></option>
                    <?php 
                    if (count($companies) > 0) {
                        foreach($companies as $company){ ?>
                            <option value = "<?php echo $company['company_id'];?>"> <?php echo $company['company_name'];?></option>				
                    <?php
                        }
                    }?>	
                </select>
            </div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Contractor</div>
            <div style="">
                    <select  name = "name_contractor" id="sales_id_contractor" style="width:31%;" class = "text">	
                        <option value=""></option>
                    <?php 
                    if (count($companies) > 0) {
                        foreach($companies as $company){ ?>
                            <option value = "<?php echo $company['company_id'];?>"> <?php echo $company['company_name'];?></option>				
                    <?php
                        }
                    }?>					
                    </select>
            </div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">Developer</div>
            <div style="">
                    <select  name = "name_developer" id="sales_id_developer" style="width:31%;" class = "text">
                        <option value=""></option>
                    <?php 
                    if (count($companies) > 0) {
                        foreach($companies as $company){ ?>
                            <option value = "<?php echo $company['company_id'];?>"> <?php echo $company['company_name'];?></option>				
                    <?php
                        }
                    }?>				
                    </select>
            </div>
    </div>
    <div style="width:95%;">
            <div style="clear:both;float:left;width:30%;" align="left">DLP</div>
            <div align="right" style="">
                <input type="text" name="name_dlp" id="sales_id_dlp" class="text">
                <input class="text" size="8" readonly="readonly" id="sales_dlp_date_display" type="text" ><input id="dlp_date_value" type="hidden" >
            </div>
    </div>
    <div style="width:95%;overflow: hidden;">
        <div style="clear:both;float:left;width:30%;margin-top: 40px;" align="left">Address Note</div>
        <div style="">
            <textarea col="8" row="9" name="name_notes" style="width: 340px; height: 100px;" id="sales_id_notes" class="text" type="text"></textarea>
        </div>
    </div>
    <div style="width:40%; clear: both;margin: auto;margin-top: 10px;">
            <input type="hidden" id="sales_status" name="sales_status" value="<?php echo $_POST['status'];?>"/>
            <input type="hidden" id="sales_address_company_id" name="address_company_id" value="<?php echo $_POST['client_id'];?>"/>
            <div style="clear:both;float:left;width:30%;margin-left:50px;" ><input type="submit" name="name_add_new_address" id="id_add" value="Add New" class="ui-button ui-state-default ui-corner-all" style="with:50px;"onclick="insertNewAddress();"></div>
            <div style="margin-right:10px;"><input type="button" name="name_close" id="id_close" value="Close" class="ui-button ui-state-default ui-corner-all" style="with:50px;" onclick="closeDialogI('div-popup');"></div>
    </div>
