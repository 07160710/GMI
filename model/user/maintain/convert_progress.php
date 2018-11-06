<?php 
header('Content-type: text/html; charset=utf-8');

require_once("../include/conn.php");
require_once("../public_param.php");
require_once("../function.php");

$sql = "SELECT id FROM project WHERE progress=1";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];
		
		$sql = "SELECT log_time FROM log WHERE (object='p' OR object='s' OR object='t' OR object='f') AND object_id='$project_id' AND content LIKE '%接单%' ORDER BY log_time DESC LIMIT 1";
		$get_log = mysql_query($sql);
		if(mysql_num_rows($get_log)){
			$l_row = mysql_fetch_array($get_log);
			$log_time = $l_row[0];
			
			$sql = "UPDATE project SET update_time='".$log_time."' WHERE id='$project_id' AND progress=1";
			if(mysql_query($sql))echo "成功更新项目 [".$project_id."] 进度<br/>";
		}
	}
}

/*$sql = "SELECT id,progress FROM project WHERE progress IN('2','3','4','5')";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)){
	while($row = mysql_fetch_array($stmt)){
		$project_id = $row[0];
		$progress = $row[1];
		
		switch($progress){
			case 2://obtain
				$sql = "SELECT u_id,process_time FROM project_apply WHERE project_id='$project_id' AND type='o' ORDER BY process_time DESC LIMIT 1";
				break;
			case 3://draft
				$sql = "SELECT u_id,process_time FROM project_apply WHERE project_id='$project_id' AND type='d' ORDER BY process_time DESC LIMIT 1";
				break;
			case 4://apply
				$sql = "SELECT u_id,process_time FROM project_apply WHERE project_id='$project_id' AND (type='e' OR type='p') ORDER BY process_time DESC LIMIT 1";
				break;
			case 5://confirm
				$sql = "SELECT u_id,process_time FROM project_apply WHERE project_id='$project_id' AND type='c' ORDER BY process_time DESC LIMIT 1";
				break;
		}
		$get_apply =  mysql_query($sql);
		if(mysql_num_rows($get_apply)){
			$a_row = mysql_fetch_array($get_apply);
			$u_id = $a_row[0];
			$time = $a_row[1];
			
			$sql = "UPDATE project SET update_by='".$u_id."',update_time='".$time."' WHERE id='$project_id' AND progress='$progress'";
			if(mysql_query($sql))echo "成功更新项目 [".$project_id."] 进度<br/>";
		}
	}
}