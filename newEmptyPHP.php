<?php


if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}
require_once('cTimeSheetEntry.class.php');
require_once('cTimeSheetManager.class.php');
require_once('cTimesheetSetting.class.php');
$data=stripslashes($_POST['data']);
$data=json_decode($data);
foreach($data as $key=>$value){
	if($key=='status') $status=$value;
	if($key=='date') $date=$value;
	if($key=='user_type') $user_type=$value;
	if($key=='level') $level=$value;
	if($key=='user_id') $user_id=$value;
	if($key=='date_from') $date_from1=$value;
	if($key=='date_to') $date_to1=$value;
}
function formatDate($date,$format){
		@$date_sql=date_create($date);
		@$date_result=date_format($date_sql,$format);
		return $date_result;
}
if($date!=''){
	$date=formatDate($date,'Y-m-d');
	$this_day = new CDate($date );
	$dd = $this_day->getDay();
	$mm = $this_day->getMonth();
	$yy = $this_day->getYear();
	$date_from=Date_calc::beginOfWeek($dd,$mm,$yy,'%Y-%m-%d ', LOCALE_FIRST_DAY );
	$date_to=Date_calc::endOfWeek ($dd,$mm,$yy, '%Y-%m-%d ', LOCALE_LAST_DAY );
	$m_11=explode("-",$date);
	$time_int_week = mktime(0,0,0,intval($m_11[1]),intval($m_11[2])+1,intval($m_11[0]));
	$week=date("W",$time_int_week);
	$m_12=explode("-",date('Y-m-d'));
	$time_int_week = mktime(0,0,0,intval($m_12[1]),intval($m_12[2])+1,intval($m_12[0]));
	$week_day=date("W",$time_int_week);
}else
{
	$date_from=$date_from1;
	$date_to=$date_to1;
	if($date_from!='')
		$date=$date_from;
	else
		$date=$date_to;
		$date=formatDate($date,'Y-m-d');
		$this_day = new CDate($date );
		$dd = $this_day->getDay();
		$mm = $this_day->getMonth();
		$yy = $this_day->getYear();
		$date_from=Date_calc::beginOfWeek($dd,$mm,$yy,'%Y-%m-%d ', LOCALE_FIRST_DAY );
		$date_to=Date_calc::endOfWeek ($dd,$mm,$yy, '%Y-%m-%d ', LOCALE_LAST_DAY );
		$m_11=explode("-",$date);
		$time_int_week = mktime(0,0,0,intval($m_11[1]),intval($m_11[2])+1,intval($m_11[0]));
		$week=date("W",$time_int_week);
		$m_12=explode("-",date('Y-m-d'));
		$time_int_week = mktime(0,0,0,intval($m_12[1]),intval($m_12[2])+1,intval($m_12[0]));
		$week_day=date("W",$time_int_week);
		$press=$week_day-$week;
		//$week=Date_calc::weekOfYear($dd,$mm,$yy);
}
//them
$timsheet_=new CTimesheetEntry();
$i=0;
$check_week=0;
$date_from_=$date_from;
$date_to_=$date_to;
//them
$submit_check_day=false;
while($date_from_<=$date_to_){
	$m_1=explode("-",$date_from_);
	//them
	$records=$timsheet_->getRecordByUserId($user_id,$date_from_,$date_from_);
	$tomorrow = mktime(0,0,0,intval($m_1[1]),intval($m_1[2])+1,intval($m_1[0]));
	$date_from_=date('Y-m-d',$tomorrow);
	$records__=$timsheet_->getRecordByUserId($user_id,$date_from,$date_to);
}
$records_=$timsheet_->getRecordByUserId($user_id,$date_from,$date_to);
if(count($records_)>0){
		foreach($records_ as $r){
			if(strtotime($r['timesheet_entry_submitted'])!=0){
				if(trim($r['timesheet_entry_type_submit'])=='week'){
					$check_week=1;
					$date_submit=formatDate($r['timesheet_entry_submitted'],'d-M-Y');
					$submit_check_day=true;
					break;
				}
			}
		}
}
$a_date=strcmp(formatDate($date,'Y-m-d'),date('Y-m-d'));	
if($status=='day'){
	$timsheet_=new CTimesheetEntry();
	$records=$timsheet_->getRecordByUserId($user_id,$date,$date);
	if(count($records)>0){
		foreach($records as $record){
			if(strtotime($record['timesheet_entry_submitted'])!=0){
				$date_submit=$record['timesheet_entry_submitted'];
				$submit_check_day=true;
				break;
			}
		}
	}
	$string_text='';
	if(!$submit_check_day){
		//if($a_date<=0){
			$string_text='<font size="2"><label id="id_date">&nbsp;'.formatDate($date,'d-M-Y').'&nbsp;</label></font>';
			$string_text=$string_text.' <label id="show_hide_button"> &nbsp; <input type="BUTTON"  class="ui-button ui-state-default ui-corner-all"  value="'.$AppUI->_('Submit').'" id="button_submit" onclick="javascript:ajax_submitTimesheet('.$user_id.',$(\'#date_select_hide\').val(),$(\'#date_select_hide\').val(),\'\',\''.$user_type.'\',\''.$level_user.'\',$(\'#value_hide_select_day\').val())"></label>';
		/*
		}else
		{
			$string_text='<font size="2"><label id="id_date">&nbsp;'.formatDate($date,'d-M-Y').'&nbsp;</label></font>';
			$string_text=$string_text.' <label id="show_hide_button"> <font color="red">&nbsp;'.$AppUI->_(' You are not allowed to add new record on').' </font>  </label>';
		}*/
	}else
	{
		$string_text=$string_text.' <label id="show_hide_button"> <input type="checkbox" checked  disabled="disabled">&nbsp;'.$AppUI->_(' Submitted on ').'&nbsp;'.formatDate($date_submit,'d-M-y').'</label>';
	}

		echo $string_text;
}else{
	$timsheet_=new CTimesheetEntry();
	$records_=$timsheet_->getRecordByUserId($user_id,$date_from,$date_to);
	if(count($records_)>0){
		foreach($records_ as $r){
			if(strtotime($r['timesheet_entry_submitted'])!=0){
				if(trim($r['timesheet_entry_type_submit'])=='week'){
					$check_week=1;
					$date_submit=formatDate($r['timesheet_entry_submitted'],'d-M-Y');
					break;
				}
			}
		}
	}	
	if($check_week==1){
		$date_from=formatDate($date_from,'d-M-y');
		$date_to=formatDate($date_to,'d-M-y');
		$string_text=' <font size="2"><label id="id_date">Week &nbsp;  '.$week.' : &nbsp;'.$date_from.' to '.$date_to.'</label></font> &nbsp;';
		$string_text=$string_text.'<label id="show_hide_button">&nbsp; <input type="checkbox" checked >'.$AppUI->_('All Submitted(Lastest submission was: ').$date_submit.')</label>';
		echo $string_text;
	}
	else 
	{
		$date_from=formatDate($date_from,'d-M-y');
		$date_to=formatDate($date_to,'d-M-y');
		$string_text=' <font size="2"><label id="id_date">'.$AppUI->_(' Week ').'&nbsp;'.$week.' : &nbsp; '.$date_from.' &nbsp;to &nbsp;'.$date_to.'</label></font>';
		if($press>=0)
			$string_text=$string_text.' <label id="show_hide_button">&nbsp;<input type="BUTTON"   class="ui-button ui-state-default ui-corner-all"   value="'.$AppUI->_('Submit All ').'"  id="button_submit" onclick="javascript:ajax_submitTimesheet('.$user_id.',$(\'#date_select_hide\').val(),$(\'#date_select_hide\').val(),\'\',\''.$user_type.'\',\''.$level_user.'\',$(\'#value_hide_select_day\').val())"></label>';
		echo $string_text;
	}
}
