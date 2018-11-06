<?php
header('Content-type: text/html; charset=utf-8');

require_once("include/conn.php");
require_once("public_param.php");
require_once("function.php");

//检查待立项前的过期合同
$sql = "SELECT id,date_expire FROM project WHERE date_sign<'".date('Y-m-d')."' AND date_expire!='0000-00-00' AND date_expire<CURDATE() ORDER BY date_expire DESC";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];
		$date_expire = $row[1];
		
		$sql = "UPDATE project SET progress=20 WHERE id='$project_id'";
		if(mysql_query($sql)){
			save_log("p",$project_id,"设为合同过期",999);
		}
	}
}

function wxmsg_remind_expire($u_id,$project_id,$time_limit){
	$user_info = get_user_info($u_id);
	$project_info = get_project_info($project_id);
	
	$msg = 	"[合同到期] ".$user_info['name']."，你所跟进的合同".$time_limit."到期：\n".
			"公司名称：".$project_info['company']."\n".
			"项目名称：".$project_info['project']."\n".
			"合同到期：".$project_info['date_expire']."\n".
			"当前进度：".$GLOBALS['project_progress_opt'][$project_info['progress']]."\n".
			"请及时跟进，若已跟进请忽略此消息。";
	send_wxqy_msg($user_info['userid'],$msg);
	
	//通知销售主管
	$sql = "SELECT id,role,branch FROM user WHERE role LIKE '%sm%'";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			$sm_id = $row[0];
			$role = $row[1];
			$branch = json_decode($row[2],true);
			
			if($sm_id!=$u_id){
				$sm_info = get_user_info($sm_id);			
				if(in_array($project_info['branch'],$branch)){
					$msg = 	"[合同到期] ".$sm_info['name']."，以下合同".$time_limit."到期：\n".
							"公司名称：".$project_info['company']."\n".
							"项目名称：".$project_info['project']."\n".
							"合同到期：".$project_info['date_expire']."\n".
							"当前进度：".$GLOBALS['project_progress_opt'][$project_info['progress']]."\n".
							"系统已通知".$user_info['name']."跟进，请知悉。";
					send_wxqy_msg($sm_info['userid'],$msg);
				}
			}
		}
	}
}

//检查即将过期的合同
$expire_lt_1_week = [];
$expire_1_week = [];
$expire_2_week = [];
$expire_3_week = [];
$expire_4_week = [];
$expire_2_month = [];
$expire_3_month = [];
$sql = "SELECT id,date_expire FROM project WHERE date_expire!='0000-00-00' AND date_expire<=DATE_ADD(CURDATE(), INTERVAL 1 MONTH) ORDER BY date_expire DESC";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];
		$date_expire = $row[1];
		//print $date_expire."|".date('Y-m-d',strtotime('+1 week'))."\n";
		if($date_expire<date('Y-m-d',strtotime('+1 week')) && $date_expire>=date('Y-m-d')){//少于1周			
			$expire_lt_1_week[$project_id] = $date_expire;
		}
		if($date_expire==date('Y-m-d',strtotime('+1 week'))){//1周
			$expire_1_week[$project_id] = $date_expire;
		}
		if($date_expire==date('Y-m-d',strtotime('+2 week'))){//2周
			$expire_2_week[$project_id] = $date_expire;
		}
		if($date_expire==date('Y-m-d',strtotime('+3 week'))){//3周
			$expire_3_week[$project_id] = $date_expire;
		}
		if($date_expire==date('Y-m-d',strtotime('+4 week'))){//4周
			$expire_4_week[$project_id] = $date_expire;
		}
		if($date_expire==date('Y-m-d',strtotime('+2 month'))){//2个月
			$expire_2_month[$project_id] = $date_expire;
		}
		if($date_expire==date('Y-m-d',strtotime('+3 month'))){//3个月
			$expire_3_month[$project_id] = $date_expire;
		}
	}
	print "< 1 week: \n";print_r($expire_lt_1_week);
	print "= 1 week: \n";print_r($expire_1_week);
	print "= 2 week: \n";print_r($expire_2_week);
	print "= 3 week: \n";print_r($expire_3_week);
	print "= 1 month: \n";print_r($expire_4_week);
	print "= 2 month: \n";print_r($expire_2_month);
	print "= 3 month: \n";print_r($expire_3_month);
	
	foreach($expire_lt_1_week as $project_id=>$date_expire){
		$sql = "SELECT u_id FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND is_curr=1";
		$u_id = mysql_result(mysql_query($sql),0);
		$unix_day = 60*60*24;
		$remain_day = round((strtotime($date_expire)-time())/$unix_day);
		$time_limit = ($remain_day>0)?"还剩".$remain_day."天":"今天";
		wxmsg_remind_expire($u_id,$project_id,$time_limit);
	}
	foreach($expire_1_week as $project_id=>$date_expire){
		$sql = "SELECT u_id FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND is_curr=1";
		$u_id = mysql_result(mysql_query($sql),0);
		$time_limit = "还剩1周";
		wxmsg_remind_expire($u_id,$project_id,$time_limit);
	}
	foreach($expire_2_week as $project_id=>$date_expire){
		$sql = "SELECT u_id FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND is_curr=1";
		$u_id = mysql_result(mysql_query($sql),0);
		$time_limit = "还剩2周";
		wxmsg_remind_expire($u_id,$project_id,$time_limit);
	}
	foreach($expire_3_week as $project_id=>$date_expire){
		$sql = "SELECT u_id FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND is_curr=1";
		$u_id = mysql_result(mysql_query($sql),0);
		$time_limit = "还剩3周";
		wxmsg_remind_expire($u_id,$project_id,$time_limit);
	}
	foreach($expire_4_week as $project_id=>$date_expire){
		$sql = "SELECT u_id FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND is_curr=1";
		$u_id = mysql_result(mysql_query($sql),0);
		$time_limit = "还剩1个月";
		wxmsg_remind_expire($u_id,$project_id,$time_limit);
	}
	foreach($expire_2_month as $project_id=>$date_expire){
		$sql = "SELECT u_id FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND is_curr=1";
		$u_id = mysql_result(mysql_query($sql),0);
		$time_limit = "还剩2个月";
		wxmsg_remind_expire($u_id,$project_id,$time_limit);
	}
	foreach($expire_3_month as $project_id=>$date_expire){
		$sql = "SELECT u_id FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND is_curr=1";
		$u_id = mysql_result(mysql_query($sql),0);
		$time_limit = "还剩3个月";
		wxmsg_remind_expire($u_id,$project_id,$time_limit);
	}
}