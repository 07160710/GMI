<?php 
header('Content-type: text/html; charset=utf-8');

require_once("../include/conn.php");
require_once("../public_param.php");
require_once("../function.php");

/*$sql = "SELECT object_id FROM log WHERE content='审核'";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)){
	while($row = mysql_fetch_array($stmt)){
		$object_id = $row[0];
		$sql = "SELECT COUNT(*) FROM project WHERE id='$object_id'";
		$has_rec = mysql_result(mysql_query($sql),0);
		if($has_rec==0){
			$sql = "UPDATE log SET object='a' WHERE object_id='$object_id' AND content='审核'";
			if(mysql_query($sql)){
				echo "Catch object [$object_id] successfully!<br/>";
			}
		}
	}
}

/*$sql = "SELECT DISTINCT sales_id FROM agreement WHERE sales_name IS NULL ORDER BY sales_id";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)){
	while($row = mysql_fetch_array($stmt)){
		$u_id = $row[0];
		if($u_id!=0){
			$sql = "SELECT name FROM user WHERE id='$u_id'";
			$u_name = mysql_result(mysql_query($sql),0);
			if($u_name==""){
				$sql = "SELECT DISTINCT name FROM project_assign WHERE u_id='$u_id'";
				$u_name = mysql_result(mysql_query($sql),0);
			}
			
			$sql = "UPDATE agreement SET sales_name='$u_name' WHERE sales_id='$u_id'";
			if(mysql_query($sql)){
				echo "Update user name [$u_name] successfully!<br/>";
			}
		}
	}
}*/

$sql = "SELECT u_id FROM project_apply WHERE (name IS NULL OR name='') AND u_id!=0";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)){
	while($row = mysql_fetch_array($stmt)){
		$u_id = $row[0];
		$sql = "SELECT name FROM user WHERE id='$u_id'";
		$u_name = mysql_result(mysql_query($sql),0);
		
		$sql = "UPDATE project_apply SET name='$u_name' WHERE (name IS NULL OR name='') AND u_id='$u_id'";
		if(mysql_query($sql)){
			echo "Update user name [$u_name] successfully!<br/>";
		}
	}
}