<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$doc = new DOMDocument('1.0', 'UTF-8');

$process = $doc->createElement("process");
$doc->appendChild($process);

$project_id = $_REQUEST['project_id'];
$project_info = get_project_info($project_id);

$apply_deadline_e = "";
$apply_deadline_p = "";
$sql = "SELECT apply_deadline_e,apply_deadline_p 
		FROM pbase 
			LEFT JOIN pbase_info ON pbase.id=pbase_info.pbase_id 
		WHERE name='".$project_info['project']."' AND year_apply='".$project_info['year_apply']."'";
$get_info = mysql_query($sql);
if(mysql_num_rows($get_info)>0){
	$row = mysql_fetch_array($get_info);
	$apply_deadline_e = $row[0];
	$apply_deadline_p = $row[1];
}

$apply_deadline_e = ($apply_deadline_e!="" && $apply_deadline_e!="0000-00-00")?"截至".$apply_deadline_e:"无截止日";
$deadline_e = $doc->createElement("apply_deadline_e",$apply_deadline_e);
$process->appendChild($deadline_e);

$apply_deadline_p = ($apply_deadline_p!="" && $apply_deadline_p!="0000-00-00")?"截至".$apply_deadline_p:"无截止日";
$deadline_p = $doc->createElement("apply_deadline_p",$apply_deadline_p);
$process->appendChild($deadline_p);

$sql = "SELECT 
			name,
			u_type,
			(SELECT name FROM user WHERE id=assign.assigned_by) AS assigner,
			start_date,
			accepted_time 
		FROM project_assign assign 
		WHERE project_id='$project_id' AND is_curr=1";
$get_assign = mysql_query($sql);
if(mysql_num_rows($get_assign)>0){
	$assign = $doc->createElement("assign");
	$process->appendChild($assign);
	
	$accept = $doc->createElement("accept");
	$process->appendChild($accept);
	
	while($row = mysql_fetch_array($get_assign)){
		$name = $row['name'];
		$u_type = $row['u_type'];
		$assigner = $row['assigner'];
		$assigned_time = ($row['start_date']!="0000-00-00")?str_replace("-","/",$row['start_date']):"";
		$accepted_time = $row['accepted_time'];
		switch($u_type){
			case "s": 
				$assign_s = $doc->createElement("assign_s",htmlspecialchars($assigner.$assigned_time."派单".$name));
				$assign->appendChild($assign_s);
				break;
			case "t": 
				$assign_t = $doc->createElement("assign_t",htmlspecialchars($assigner.$assigned_time."派单".$name));
				$assign->appendChild($assign_t);
				
				if($accepted_time>0){
					$accept_t = $doc->createElement("accept_t",htmlspecialchars($name.date('Y/m/d',$accepted_time)."接单"));
					$accept->appendChild($accept_t);
				}
				break;
			case "f": 
				$assign_f = $doc->createElement("assign_f",htmlspecialchars($assigner.$assigned_time."派单".$name));
				$assign->appendChild($assign_f);
				
				if($accepted_time>0){
					$accept_f = $doc->createElement("accept_f",htmlspecialchars($name.date('Y/m/d',$accepted_time)."接单"));
					$accept->appendChild($accept_f);
				}
				break;
		}
	}
}

$apply = $doc->createElement("apply");
$process->appendChild($apply);

$request_t = "";
$sql = "SELECT 
			(SELECT name FROM user WHERE id=message.from_user) AS from_user,
			(SELECT name FROM user WHERE id=message.to_user) AS to_user,
			sent_time,
			read_time 
		FROM message 
		WHERE project_id='$project_id' AND from_u_type='t'";
$get_request = mysql_query($sql);
if(mysql_num_rows($get_request)>0){
	while($r_row = mysql_fetch_array($get_request)){
		$from_user = $r_row['from_user'];
		$to_user = $r_row['to_user'];
		$sent_time = date('m/d',$r_row['sent_time']);
		$read_time = $r_row['read_time'];
		
		$request_t .= "	<li>
							<span class=\"request_info\">".$from_user.$sent_time."请求</span>
							".(($read_time>0)?"<sapn class=\"read_info\">".$to_user.date('m/d',$read_time)."阅</span>":"")."
						</li>";
	}
	$request_t = "<ul style=\"margin-bottom:3px;\">$request_t</ul>";
	$request = $doc->createElement("apply_t_request",htmlspecialchars($request_t));
	$apply->appendChild($request);
}

$request_f = "";
$sql = "SELECT 
			(SELECT name FROM user WHERE id=message.from_user) AS from_user,
			(SELECT name FROM user WHERE id=message.to_user) AS to_user,
			sent_time,
			read_time 
		FROM message 
		WHERE project_id='$project_id' AND from_u_type='f'";
$get_request = mysql_query($sql);
if(mysql_num_rows($get_request)>0){
	while($r_row = mysql_fetch_array($get_request)){
		$from_user = $r_row['from_user'];
		$to_user = $r_row['to_user'];
		$sent_time = date('m/d',$r_row['sent_time']);
		$read_time = $r_row['read_time'];
		
		$request_f .= "	<li>
							<span class=\"request_info\">".$from_user.$sent_time."请求</span>
							".(($read_time>0)?"<sapn class=\"read_info\">".$to_user.date('m/d',$read_time)."阅</span>":"")."
						</li>";
	}
	$request_f = "<ul style=\"margin-bottom:3px;\">$request_f</ul>";
	$request = $doc->createElement("apply_f_request",htmlspecialchars($request_f));
	$apply->appendChild($request);
}

$status_apply = json_decode($project_info['status_apply'],true);

$sql = "SELECT 
			name,
			u_type,
			type,
			process_time 
		FROM project_apply 
		WHERE project_id='$project_id'";
$get_apply = mysql_query($sql);
if(mysql_num_rows($get_apply)>0){
	while($row = mysql_fetch_array($get_apply)){
		$name = $row['name'];
		$u_type = $row['u_type'];
		$type = $row['type'];
		$process_time = $row['process_time'];
		$confirm_by = $row['confirm_by'];
		$confirm_time = $row['confirm_time'];
		switch($u_type){
			case "t": 
				switch($type){
					case "o": 
						if($status_apply['t']['o']==1){
							$apply_t_o = $doc->createElement("apply_t_o",htmlspecialchars($name.date('Y/m/d',$process_time)."完成"));
							$apply->appendChild($apply_t_o);
						}
						break;
					case "d": 
						if($status_apply['t']['d']==1){
							$apply_t_d = $doc->createElement("apply_t_d",htmlspecialchars($name.date('Y/m/d',$process_time)."完稿"));
							$apply->appendChild($apply_t_d);
						}
						break;
					case "e": 
						if($status_apply['t']['e']==1){
							$apply_t_e = $doc->createElement("apply_t_e",htmlspecialchars($name.date('Y/m/d',$process_time)."提交"));
							$apply->appendChild($apply_t_e);
						}
						if($status_apply['t']['e']==2){
							$apply_t_e = $doc->createElement("apply_t_e",htmlspecialchars("无需提交"));
							$apply->appendChild($apply_t_e);
						}
						break;
					case "p": 
						if($status_apply['t']['p']==1){
							$apply_t_p = $doc->createElement("apply_t_p",htmlspecialchars($name.date('Y/m/d',$process_time)."提交"));
							$apply->appendChild($apply_t_p);
						}
						if($status_apply['t']['p']==2){
							$apply_t_p = $doc->createElement("apply_t_p",htmlspecialchars("无需提交"));
							$apply->appendChild($apply_t_p);
						}
						break;
					case "c": 
						if($status_apply['t']['c']==1){
							$apply_t_c = $doc->createElement("apply_t_c",htmlspecialchars($name.date('Y/m/d',$process_time)."确认"));
							$apply->appendChild($apply_t_c);
						}
						break;
				}
				break;
			case "f": 
				switch($type){
					case "o": 
						if($status_apply['f']['o']==1){
							$apply_f_o = $doc->createElement("apply_f_o",htmlspecialchars($name.date('Y/m/d',$process_time)."完成"));
							$apply->appendChild($apply_f_o);
						}
						break;
					case "d": 
						if($status_apply['f']['d']==1){
							$apply_f_d = $doc->createElement("apply_f_d",htmlspecialchars($name.date('Y/m/d',$process_time)."完稿"));
							$apply->appendChild($apply_f_d);
						}
						break;
					case "e": 
						if($status_apply['f']['e']==1){
							$apply_f_e = $doc->createElement("apply_f_e",htmlspecialchars($name.date('Y/m/d',$process_time)."提交"));
							$apply->appendChild($apply_f_e);
						}
						if($status_apply['f']['e']==2){
							$apply_f_e = $doc->createElement("apply_f_e",htmlspecialchars("无需提交"));
							$apply->appendChild($apply_f_e);
						}
						break;
					case "p": 
						if($status_apply['f']['p']==1){
							$apply_f_p = $doc->createElement("apply_f_p",htmlspecialchars($name.date('Y/m/d',$process_time)."提交"));
							$apply->appendChild($apply_f_p);
						}
						if($status_apply['f']['p']==2){
							$apply_f_p = $doc->createElement("apply_f_p",htmlspecialchars("无需提交"));
							$apply->appendChild($apply_f_p);
						}
						break;
					case "c": 
						if($status_apply['f']['c']==1){
							$apply_f_c = $doc->createElement("apply_f_c",htmlspecialchars($name.date('Y/m/d',$process_time)."确认"));
							$apply->appendChild($apply_f_c);
						}
						break;
				}
				break;
		}
	}
}

// $sql = "SELECT need_approve,need_check,need_fund FROM project WHERE id = $project_id";
// $get_need_action = mysql_query($sql);
// if(mysql_num_rows($get_need_action)){
// 	$need_action = mysql_fetch_array($get_need_action);
// }

// $sql = "SELECT id,type,status,remark FROM project_action WHERE project_id = $project_id";
// $get_project_action = mysql_query($sql);
// if(mysql_num_rows($get_project_action)){
// 	while($row = mysql_fetch_array($get_project_action)){
// 		$project_action[$row['type']] = array('status'=>$row['status'],'id'=>$row['id'],'remark'=>$row['remark']);
// 	}
// }
//项目立项
$sql = "SELECT 
			progress,
			batch,
			bonus,
			project_approval.remark,
			result
		FROM project 
			LEFT JOIN project_approval ON project.id=project_approval.project_id 
		WHERE project_id='$project_id'";
$get_approve = mysql_query($sql);
if(mysql_num_rows($get_approve)>0){
	$row = mysql_fetch_array($get_approve);
	$progress = $row[0];
	$batch = $row[1];
	$bonus = $row[2];
	$remark = $row[3];

	$approve_str = "";
	if($progress==6||$row['result']!=2){ //立项成功
		$approve_str = "1|";
		$approve_str .= ($batch!="")?$batch."立项":"";
		$approve_str .= ($bonus>0)?(($approve_str!="")?"，":"")."奖补".$bonus."万":"";						
		$approve_str .= ($remark!="")?(($approve_str!="")?"，":"").$remark:"";
	}
	if($progress==7||$row['result']==2){//立项失败
		$approve_str = "2|";
		$approve_str .= $remark;
	}
	$approve = $doc->createElement("approve",htmlspecialchars($approve_str));
	$process->appendChild($approve);
	
}
//项目验收
$sql = "SELECT id,result,reason,created_by FROM project_check WHERE project_id = $project_id ORDER BY id ASC";
$get_check = mysql_query($sql);
if(mysql_num_rows($get_check)>0){
	$i = 1;
	while($row = mysql_fetch_array($get_check)){
		$e_name = 'check'.$i;
		$e_name = $doc->createElement($e_name,htmlspecialchars($row['result'].'|'.$row['reason']));
		$process->appendChild($e_name);
		$i++;
	}

}
//请款、企业收款、中科回款
$sql = "SELECT id,people,`date`,`money`,type,remark FROM project_fund WHERE project_id = $project_id";
$get_fund = mysql_query($sql);
if(mysql_num_rows($get_fund)){
	while($row = mysql_fetch_array($get_fund)){
		$project_fund[$row['type']] = array(
										'id'=>$row['id'],
										'people'=>$row['people'],
										'date'=>$row['date'],
										'remark'=>$row['remark'],
										'money'=>$row['money']
									);
	}
}


if(isset($project_fund['rf'])){ //请款
	$e_val = $project_fund['rf']['people'].'于'.$project_fund['rf']['date'].'请款成功';
	$request_fund = $doc->createElement("request_fund",htmlspecialchars($e_val));
	$process->appendChild($request_fund);
}


if(isset($project_fund['cf'])){ //企业收款
	$e_val = '企业于'.$project_fund['cf']['date'].'收款'.$project_fund['cf']['money'];
	$receive_fund = $doc->createElement("receive_fund",htmlspecialchars($e_val));
	$process->appendChild($receive_fund);
}

if(isset($project_fund['zf'])){ //中科回款
	$e_val = $project_fund['zf']['people'].'于'.$project_fund['zf']['date'].'回款金额：'.$project_fund['zf']['money'];
	$receive_fee = $doc->createElement("receive_fee",htmlspecialchars($e_val));
	$process->appendChild($receive_fee);
}
  
echo $doc->saveXML();
?>