<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if($_POST['action']=="add"){
	$new_id = get_new_id("pbase");
	$insert_key_str = "";
	$insert_val_str = "";
	foreach($GLOBALS['pbase_fields'] as $key){
		if($key!="id"){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
			
			if($key=="name"){
				$sql = "SELECT COUNT(*) FROM pbase WHERE name='$name'";
				$has_pbase = mysql_result(mysql_query($sql),0);
				if($has_pbase){
					$arr = array(
						'success'=>0,
						'error'=>"此基础项目名称已存在，请勿重复添加！"
					);
					echo json_encode($arr);
					exit;
				}
			}
			
			if($insert_key_str!="")$insert_key_str .= ",";
			$insert_key_str .= $key;
			
			if($insert_val_str!="")$insert_val_str .= ",";		
			$insert_val_str .= "'".${$key}."'";
		}
	}
	
	$sql = "INSERT INTO pbase(
				id,
				$insert_key_str
			) VALUES(
				'$new_id',
				$insert_val_str
			)";
	if(mysql_query($sql)){
		if(isset($_POST['info'])){
			foreach($_POST['info'] as $info_id){
				$year_apply = mysql_escape_string($_POST['year_apply_'.$info_id]);
				$level = mysql_escape_string($_POST['level_'.$info_id]);
				$province = mysql_escape_string($_POST['province_'.$info_id]);
				$city = mysql_escape_string($_POST['city_'.$info_id]);
				$district = mysql_escape_string($_POST['district_'.$info_id]);
				$apply_deadline_e = mysql_escape_string($_POST['apply_deadline_e_'.$info_id]);
				$apply_deadline_p = mysql_escape_string($_POST['apply_deadline_p_'.$info_id]);
				
				$region = $province.",".$city.",".$district;
				
				$sql = "INSERT INTO pbase_info(
							pbase_id,
							year_apply,
							level,
							region,
							apply_deadline_e,
							apply_deadline_p
						) VALUES(
							'$new_id',
							'$year_apply',
							'$level',
							'$region',
							'$apply_deadline_e',
							'$apply_deadline_p'
						)";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"保存基础项目信息出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"添加基础项目出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("pb",$new_id,"创建");
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="edit"){
	$id = mysql_escape_string($_POST['id']);
	$update_field_str = "";
	foreach($GLOBALS['pbase_fields'] as $key){
		if($key!="id"){
			${$key} = mysql_escape_string($_POST['v_'.$key]);
		
			if($update_field_str!="")$update_field_str .= ",";
			$update_field_str .= "$key='".${$key}."'";
		}
	}
	
	$sql = "UPDATE pbase 
			SET $update_field_str  
			WHERE id='$id'";
	if(mysql_query($sql)){
		$db_info_arr = [];
		$sql = "SELECT year_apply,level,region FROM pbase_info WHERE pbase_id='$id'";
		$get_db_info = mysql_query($sql);
		if(mysql_num_rows($get_db_info)>0){
			while($row = mysql_fetch_array($get_db_info)){
				$db_info_arr[] = $row[0]."|".$row[1]."|".$row[2];
			}
		}
		
		$info_arr = [];
		if(isset($_POST['info'])){
			foreach($_POST['info'] as $info_id){
				$year_apply = mysql_escape_string($_POST['year_apply_'.$info_id]);
				$level = mysql_escape_string($_POST['level_'.$info_id]);
				$province = mysql_escape_string($_POST['province_'.$info_id]);
				$city = mysql_escape_string($_POST['city_'.$info_id]);
				$district = mysql_escape_string($_POST['district_'.$info_id]);
				$apply_deadline_e = mysql_escape_string($_POST['apply_deadline_e_'.$info_id]);
				$apply_deadline_p = mysql_escape_string($_POST['apply_deadline_p_'.$info_id]);
				
				$region = $province.",".$city.",".$district;
				$info_arr[] = $year_apply."|".$level."|".$region;
				
				$sql = "SELECT COUNT(*) FROM pbase_info WHERE pbase_id='$id' AND year_apply='$year_apply' AND level='$level' AND region='$region'";
				$has_rec = mysql_result(mysql_query($sql),0);
				if($has_rec==0){
					$sql = "INSERT INTO pbase_info(
								pbase_id,
								year_apply,
								level,
								region,
								apply_deadline_e,
								apply_deadline_p
							) VALUES(
								'$id',
								'$year_apply',
								'$level',
								'$region',
								'$apply_deadline_e',
								'$apply_deadline_p'
							)";
				}
				else{
					$sql = "UPDATE pbase_info 
							SET region='$region',
								apply_deadline_e='$apply_deadline_e',
								apply_deadline_p='$apply_deadline_p' 
							WHERE pbase_id='$id' AND year_apply='$year_apply' AND level='$level' AND region='$region'";
				}
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"保存基础项目信息出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
		foreach($db_info_arr as $key){
			if(!in_array($key,$info_arr)){
				$key_arr = explode("|",$key);
				$sql = "DELETE FROM pbase_info WHERE pbase_id='$id' AND year_apply='".$key_arr[0]."' AND level='".$key_arr[1]."' AND region='".$key_arr[2]."'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除基础项目信息出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"更新基础项目出错：".mysql_error()
		);
		echo json_encode($arr);
		exit;
	}
	
	save_log("pb",$id,"更新");
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="delete_pbase"){
	if(isset($_REQUEST['scope']) && $_REQUEST['scope']=="all"){
		$cond_str = "";
		include_once("fetch_pbase_cond.php");
		$sql = "SELECT id,name 
				FROM pbase 
				$cond_str";		
		$get_pbase = mysql_query($sql);
		if(mysql_num_rows($get_pbase)>0){
			while($row = mysql_fetch_array($get_pbase)){
				$id = $row[0];
				$name = $row[1];
				
				$sql = "DELETE FROM pbase WHERE id='$id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除基础项目出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				
				$sql = "DELETE FROM pbase_info WHERE pbase_id='$id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除基础项目信息出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	else{
		if(isset($_REQUEST['pbase_id'])){
			foreach($_REQUEST['pbase_id'] as $pbase_id){
				$pbase_id = mysql_escape_string($pbase_id);
				
				$sql = "SELECT name FROM pbase WHERE id='$pbase_id'";
				$name = mysql_result(mysql_query($sql),0);
				
				$sql = "DELETE FROM pbase WHERE id='$pbase_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除基础项目出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
				
				$sql = "DELETE FROM pbase_info WHERE pbase_id='$pbase_id'";
				if(!mysql_query($sql)){
					$arr = array(
						'success'=>0,
						'error'=>"删除基础项目信息出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}
