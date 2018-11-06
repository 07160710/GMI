<?php
header('Content-type: text/html; charset=utf-8');

require_once("include/conn.php");
require_once("public_param.php");
require_once("function.php");

$tm_arr = [];
$fm_arr = [];
$sql = "SELECT id,role 
		FROM user 
		WHERE role LIKE '%sm%' OR role LIKE '%tm%' OR role LIKE '%fm%'";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$u_id = $row[0];
		$role = $row[1];
		
		if(strpos($role,'tm')!==false)$tm_arr[] = $u_id;
		if(strpos($role,'fm')!==false)$fm_arr[] = $u_id;
	}
}

/* 提醒派单，每周一提醒 */
if(date('w')==1){
	foreach($tm_arr as $u_id){
		$user_info = get_user_info($u_id);
		
		$branch_cond = "";
		if($user_info['branch']!=""){
			$branch_arr = json_decode($user_info['branch'],true);		
			foreach($branch_arr as $branch){
				if($branch_cond!="")$branch_cond .= ",";
				$branch_cond .= "'$branch'";
			}
		}
		else $branch_cond = "''";
		
		$unassign_t_arr = [];
		$sql = "SELECT id 
				FROM project 
				WHERE year_apply='".date('Y')."' 
					AND progress=0 
					AND (status_assign=0 OR status_assign=1 OR status_assign=3 OR status_assign=6) 
					AND branch IN (".$branch_cond.")";
		$get_unassign = mysql_query($sql);
		$unassign_count = mysql_num_rows($get_unassign);
		if($unassign_count>0){
			while($row = mysql_fetch_array($get_unassign)){
				$project_id = $row[0];
				
				$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='t' AND is_curr=1";
				$has_assign = mysql_result(mysql_query($sql),0);
				if($has_assign==0)$unassign_t_arr[] = $project_id;
			}
		}
		
		if(count($unassign_t_arr)>0){
			$msg = 	"[派单提醒] ".$user_info['name']."，你有".count($unassign_t_arr)."个项目未安排技术人员，请进入协同平台安排技术人员。";echo $msg."<br/>";
			send_wxqy_msg($user_info['userid'],$msg);
		}
	}
	foreach($fm_arr as $u_id){
		$user_info = get_user_info($u_id);
		
		$branch_cond = "";
		if($user_info['branch']!=""){
			$branch_arr = json_decode($user_info['branch'],true);		
			foreach($branch_arr as $branch){
				if($branch_cond!="")$branch_cond .= ",";
				$branch_cond .= "'$branch'";
			}
		}
		else $branch_cond = "''";
		
		$unassign_f_arr = [];
		$sql = "SELECT id 
				FROM project 
				WHERE year_apply='".date('Y')."' 
					AND progress=0 
					AND (status_assign=0 OR status_assign=1 OR status_assign=2 OR status_assign=5) 
					AND branch IN (".$branch_cond.")";
		$get_unassign = mysql_query($sql);
		$unassign_count = mysql_num_rows($get_unassign);
		if($unassign_count>0){
			while($row = mysql_fetch_array($get_unassign)){
				$project_id = $row[0];
				
				$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='f' AND is_curr=1";
				$has_assign = mysql_result(mysql_query($sql),0);
				if($has_assign==0)$unassign_f_arr[] = $project_id;
			}
		}
		
		if(count($unassign_f_arr)>0){
			$msg = 	"[派单提醒] ".$user_info['name']."，你有".count($unassign_f_arr)."个项目未安排财务人员，请进入协同平台安排财务人员。";echo $msg."<br/>";
			send_wxqy_msg($user_info['userid'],$msg);
		}
	}
}

/* 提醒接单，派单后第三天提醒 */
function get_assign_info($project_id, $u_id, $u_type){
	$info = array();
	
	$sql = "SELECT user.name AS assigned_by,start_date 
			FROM project_assign assign 
				LEFT JOIN user ON assign.assigned_by=user.id 
			WHERE project_id='$project_id' 
				AND u_id='$u_id' 
				AND u_type='$u_type' 
				AND is_curr=1";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		$row = mysql_fetch_array($stmt);
		$info['assigned_by'] = $row[0];
		$info['assigned_time'] = $row[1];
	}
	return $info;
}

$sql = "SELECT id 
		FROM project 
		WHERE year_apply='".date('Y')."' 
			AND progress=0 
			AND (status_assign=4 OR status_assign=5 OR status_assign=6)";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];		
		$project_info = get_project_info($project_id);
		
		$sql = "SELECT u_id FROM project_assign WHERE project_id='$project_id' AND u_type='t' AND is_curr=1 AND accepted_time=0";
		$ts_id = mysql_result(mysql_query($sql),0);
		if($ts_id>0){
			$user_info = get_user_info($ts_id);
			$assign_info = get_assign_info($project_id,$ts_id,"t");
			
			$assigned_time = $assign_info['assigned_time'];
			$assigned_date = strtotime($assigned_time." 00:00:00");
			$today = strtotime(date('Y-m-d 00:00:00'));
			
			if($assigned_time!="0000-00-00" && ($today-$assigned_date)==60*60*24*3){//第三天
				$msg = 	"[接单提醒] ".$user_info['name']."，你已于".$assigned_time."被".$assign_info['assigned_by']."安排跟进以下项目：\n".
						"公司名称：".$project_info['company']."\n".
						"项目名称：".$project_info['project']."\n".
						"请点击 <a href='"._BASE_URL_."task?id=".$project_id."'>这里</a> 接单。";echo $msg."<br/>";
				send_wxqy_msg($user_info['userid'],$msg);
			}
		}
		
		$sql = "SELECT u_id FROM project_assign WHERE project_id='$project_id' AND u_type='f' AND is_curr=1 AND accepted_time=0";
		$fs_id = mysql_result(mysql_query($sql),0);
		if($fs_id>0){
			$user_info = get_user_info($fs_id);
			$assign_info = get_assign_info($project_id,$fs_id,"f");
			
			$assigned_time = $assign_info['assigned_time'];
			$assigned_date = strtotime($assigned_time." 00:00:00");
			$today = strtotime(date('Y-m-d 00:00:00'));
			
			if($assigned_time!="0000-00-00" && ($today-$assigned_date)==60*60*24*3){//第三天
				$msg = 	"[接单提醒] ".$user_info['name']."，你已于".$assigned_time."被".$assign_info['assigned_by']."安排跟进以下项目：\n".
						"公司名称：".$project_info['company']."\n".
						"项目名称：".$project_info['project']."\n".
						"请点击 <a href='"._BASE_URL_."task?id=".$project_id."'>这里</a> 接单。";echo $msg."<br/>";
				send_wxqy_msg($user_info['userid'],$msg);
			}
		}
	}
}

/* 提醒确认，每月最后一日提醒 */
if(date('Y-m-d')==date('Y-m-d',strtotime(date('Y-m-01',strtotime('+1 months'))." -1 days"))){
	foreach($tm_arr as $u_id){
		$user_info = get_user_info($u_id);
		
		$branch_cond = "";
		if($user_info['branch']!=""){
			$branch_arr = json_decode($user_info['branch'],true);		
			foreach($branch_arr as $branch){
				if($branch_cond!="")$branch_cond .= ",";
				$branch_cond .= "'$branch'";
			}
		}
		else $branch_cond = "''";	
		
		$sql = "SELECT id,status_assign,status_apply FROM project WHERE progress=4 AND status_apply!='' AND branch IN (".$branch_cond.")";
		$stmt = mysql_query($sql);
		if(mysql_num_rows($stmt)>0){
			$msg = "";
			$i = 0;
			while($row = mysql_fetch_array($stmt)){
				$project_id = $row[0];
				$status_assign = $row[1];
				$status_apply = $row[2];
				$project_info = get_project_info($project_id);
				$status_apply = json_decode($status_apply,true);
				if($status_apply['t']['e']>0 && $status_apply['t']['p']>0 && $status_apply['t']['c']==0){//技术待确认
					$msg .= sprintf("%02d",$i+1).") ".$project_info['company']."：".$project_info['project']."\n";
					$i++;
				}
			}
			if($msg!=""){
				$msg = 	"[确认提醒] ".$user_info['name']."，以下".$i."个项目等待你进行申报确认：\n".
						$msg.
						"请到协同后台“技术”栏目确认。";echo $msg."<br/>";
				send_wxqy_msg($user_info['userid'],$msg);
			}
		}
	}
	foreach($fm_arr as $u_id){
		$user_info = get_user_info($u_id);
		
		$branch_cond = "";
		if($user_info['branch']!=""){
			$branch_arr = json_decode($user_info['branch'],true);		
			foreach($branch_arr as $branch){
				if($branch_cond!="")$branch_cond .= ",";
				$branch_cond .= "'$branch'";
			}
		}
		else $branch_cond = "''";	
		
		$sql = "SELECT id,status_assign,status_apply FROM project WHERE progress=4 AND status_apply!='' AND branch IN (".$branch_cond.")";
		$stmt = mysql_query($sql);
		if(mysql_num_rows($stmt)>0){
			$msg = "";
			$i = 0;
			while($row = mysql_fetch_array($stmt)){
				$project_id = $row[0];
				$status_assign = $row[1];
				$status_apply = $row[2];
				$project_info = get_project_info($project_id);
				$status_apply = json_decode($status_apply,true);
				if($status_apply['f']['e']>0 && $status_apply['f']['p']>0 && $status_apply['f']['c']==0){//财务待确认
					$msg .= sprintf("%02d",$i+1).") ".$project_info['company']."：".$project_info['project']."\n";
					$i++;
				}
			}
			if($msg!=""){
				$msg = 	"[确认提醒] ".$user_info['name']."，以下".$i."个项目等待你进行申报确认：\n".
						$msg.
						"请到协同后台“财务”栏目确认。";echo $msg."<br/>";
				send_wxqy_msg($user_info['userid'],$msg);
			}
		}
	}
}