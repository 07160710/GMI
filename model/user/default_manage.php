<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_POST['action']=="edit"){
	$s_id = mysql_escape_string($_POST['site_id']);
	$s_name = mysql_escape_string($_POST['s_name']);
	$s_title = mysql_escape_string($_POST['s_title']);
	$s_domain = mysql_escape_string($_POST['s_domain']);
	$s_layout = mysql_escape_string($_POST['s_layout']);
	
	$s_redirect = mysql_escape_string($_POST['s_redirect']);
	$s_meta_desc = mysql_escape_string($_POST['s_meta_desc']);
	$s_meta_kwrd = mysql_escape_string($_POST['s_meta_kwrd']);
	
	$s_publish = 0;
	if($_POST['s_publish']=="on")$s_publish = 1;
	
	$s_layout_header = "";
	foreach($_POST['header'] as $val){
		if($s_layout_header!="")$s_layout_header .= "#";
		$s_layout_header .= $val;
	}
	
	$layout_num = 1;
	switch($s_layout){
		case 'two_col_50_50': $layout_num = 2; break;
		case 'two_col_30_70': $layout_num = 2; break;
		case 'two_col_70_30': $layout_num = 2; break;
		case 'one_two_col_30_70': $layout_num = 3; break;
		case 'one_two_col_70_30': $layout_num = 3; break;
		case 'two_two_col': $layout_num = 4; break;
		case 'three_col': $layout_num = 3; break;
		default: $layout_num = 1;
	}
	$s_layout_body = "";
	for($i=0;$i<$layout_num;$i++){
		if(isset($_POST['body_'.$i])){
			if($s_layout_body!="")$s_layout_body .= "|";
			$layout_node_str = "";
			foreach($_POST['body_'.$i] as $val){
				if($layout_node_str!="")$layout_node_str .= "#";
				$layout_node_str .= $val;
			}
			$s_layout_body .= $layout_node_str;
		}
	}
	
	$s_layout_footer = "";
	foreach($_POST['footer'] as $val){
		if($s_layout_footer!="")$s_layout_footer .= "#";
		$s_layout_footer .= $val;
	}
	
	$insert_field_str = "";
	$insert_value_str = "";
	$update_field_str = "";
	foreach($default_fields as $val){
		if($val!="logo"){
			if($insert_field_str!="")$insert_field_str .= ",";
			$insert_field_str .= $val;
			
			if($insert_value_str!="")$insert_value_str .= ",";
			$insert_value_str .= "'".${"s_".$val}."'";
			
			if($update_field_str!="")$update_field_str .= ",";
			$update_field_str .= "$val = '".${"s_".$val}."'";
		}
	}
	
	$sql = "UPDATE site_table SET $update_field_str WHERE id='$s_id'";
	if(mysql_query($sql)){
		$arr = array(
			'success'=>1,
			'id'=>$s_id
		);		
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"保存站点设定失败: ".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="publish"){
	$s_id = mysql_escape_string($_POST['s_id']);
	
	$get_site_query = "SELECT * FROM site_table WHERE id='$s_id'";
	$get_site = mysql_query($get_site_query);
	if(mysql_num_rows($get_site)>0){
		$s_row = mysql_fetch_array($get_site);
		
		$publish_site_query = "";
		if(check_publish($s_id,"site")==0){//insert record
			$insert_field_str = "";
			$insert_value_str = "";
			foreach($pub_site_fields as $val){
				if($insert_field_str!="")$insert_field_str .= ",";
				$insert_field_str .= $val;
				
				if($insert_value_str!="")$insert_value_str .= ",";
				$insert_value_str .= "'".mysql_escape_string($s_row[$val])."'";
			}
		
			$publish_site_query = "	INSERT INTO pub_site_table(
										$insert_field_str
									) VALUES(
										$insert_value_str
									)";
		}
		else{//update record
			$update_field_str = "";
			foreach($pub_site_fields as $val){
				if($val!="id"){
					if($update_field_str!="")$update_field_str .= ",";
					$update_field_str .= "$val = '".mysql_escape_string($s_row[$val])."'";
				}
			}
		
			$publish_site_query = "UPDATE pub_site_table SET $update_field_str WHERE id='$s_id'";
		}
		
		if(mysql_query($publish_site_query)){
			$arr = array(
				'action'=>$_POST['action'],
				'success'=>1,
				'id'=>$s_id
			);
		}
		else{
			$arr = array(
				'action'=>$_POST['action'],
				'success'=>0,
				'error'=>"Error in publishing site info: ".mysql_error()
			);
		}	
		echo json_encode($arr);
		exit;
	}
}

if($_POST['action']=="unpublish"){
	$s_id = mysql_escape_string($_POST['s_id']);
	
	$unpublish_site_query = "DELETE FROM pub_site_table WHERE id='$s_id'";
	if(mysql_query($unpublish_site_query)){
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>1,
			'id'=>$s_id
		);
	}
	else{
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>0,
			'error'=>"Error in unpublishing site info: ".mysql_error()
		);
	}	
	echo json_encode($arr);
	exit;
}
?>