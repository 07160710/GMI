<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_REQUEST['parent_id']!=""){
	$tb_name = "";
	if(isset($_REQUEST['n_id'])){
		$tb_name = "content_table";
		$check_id = mysql_escape_string($_REQUEST['n_id']);
	}
	else if(isset($_REQUEST['m_id'])){
		$tb_name = "media_table";
		$check_id = mysql_escape_string($_REQUEST['m_id']);
	}
	else if(isset($_REQUEST['dt_id'])){
		$tb_name = "data_table";
		$check_id = mysql_escape_string($_REQUEST['dt_id']);
	}
	
	$parent_id = mysql_escape_string($_REQUEST['parent_id']);
	$alias = mysql_escape_string($_REQUEST['alias']);
	
	$sql = "SELECT COUNT(*) 
			FROM ".$tb_name." 
			WHERE parent_id='$parent_id' AND 
				id!='$check_id' AND 
				alias='$alias'";
	if(mysql_result(mysql_query($sql),0)>0){
		$arr = array(
			'success'=>0
		);
		echo json_encode($arr);
		exit;
	}	
}
?>