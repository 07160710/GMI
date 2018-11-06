<?php 
header('Content-type: text/html; charset=utf-8');

require_once("../include/conn.php");
require_once("../public_param.php");
require_once("../function.php");

$sql = "SELECT project_id,u_type,u_id,name FROM project_assign WHERE start_date='0000-00-00' AND is_curr=1";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];
		$u_type = $row[1];
		$u_id = $row[2];
		$name = $row[3];
		
		$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='$u_type' AND u_id!='$u_id' AND name!='$name'";
		$has_curr = mysql_result(mysql_query($sql),0);
		if($has_curr>0){
			$sql = "UPDATE project_assign SET is_curr=0 WHERE project_id='$project_id' AND u_type='$u_type' AND u_id='$u_id' AND name='$name'";
			if(mysql_query($sql)){
				echo "成功更新项目[$project_id]当前负责<br/>";
			}
		}
	}
}


/*$sql = "UPDATE project_assign SET is_curr=0 WHERE start_date='0000-00-00' AND (u_type='s' OR u_type='t' OR u_type='f')";
if(mysql_query($sql)){
	echo "成功更新派单状态<br/>";
}

/*$sql = "SELECT DISTINCT project_id FROM project_assign WHERE start_date='0000-00-00' AND (u_type='s' OR u_type='t' OR u_type='f') AND is_curr=1 ORDER BY project_id";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];
		$project_info = get_project_info($project_id);
		$date_sign = $project_info['date_sign'];
		$assigned_time = strtotime($date_sign." 09:00:00");
		
		$sql = "UPDATE project_assign SET start_date='$date_sign',assigned_by='999',assigned_time='$assigned_time' WHERE project_id='$project_id' AND is_curr=1";
		if(mysql_query($sql)){
			echo "成功更新项目[".$project_info['project']."]派单时间 $date_sign<br/>";
		}
	}
}