<?php
header('Content-type: text/html; charset=utf-8');

//require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$sql = "SELECT id,status_assign FROM project ORDER BY date_sign DESC";
$get_project = mysql_query($sql);
if(mysql_num_rows($get_project)>0){
	while($row = mysql_fetch_array($get_project)){
		$project_id = $row[0];
		$status_assign = $row[1];
		
		$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='s' AND is_curr=1";
		$has_sales = mysql_result(mysql_query($sql),0);

		$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='t' AND is_curr=1";
		$has_technology = mysql_result(mysql_query($sql),0);

		$sql = "SELECT COUNT(*) FROM project_assign WHERE project_id='$project_id' AND u_type='f' AND is_curr=1";
		$has_finance = mysql_result(mysql_query($sql),0);
		
		if($status_assign!=5){
			$status_assign = 0;
			if($has_technology>0 && $has_finance>0)$status_assign = 4;
			else if($has_technology>0 && $has_finance==0)$status_assign = 2;
			else if($has_technology==0 && $has_finance>0)$status_assign = 3;
			else $status_assign = 1;
		}
		
		$sql = "UPDATE project SET status_assign='$status_assign' WHERE id='$project_id'";
		if(!mysql_query($sql)){
			echo "更新派单状态出错：".mysql_error()."\n";
		}
	}
	echo "更新派单状态完毕！";
}

