<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if(isset($_POST['action'])){
	if($_SESSION['wx_token']==""){//获取access_token
		if($_SESSION['wx_info']=="")$_SESSION['wx_info'] = get_qywx();
		$url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$_SESSION['wx_info']['appid'].'&corpsecret='.$_SESSION['wx_info']['appsecret'];		
		$result = https_post($url);
		$result = json_decode($result,true);	
		if(array_key_exists("errcode",$result)){
			if($result['errcode']!=0){
				$arr = array(
					'success'=>0,
					'error'=>$result['errcode'].":".$result['errmsg']
				);
				echo json_encode($arr);
				exit;
			}
			$_SESSION['wx_token'] = $result['access_token'];
		}
	}
}

if($_POST['action']=="import_member"){
	$dept_import_num = 0;
	$dept_insert_num = 0;
	$dept_update_num = 0;
	$dept_delete_num = 0;
	$user_import_num = 0;
	$user_insert_num = 0;
	$user_update_num = 0;
	$user_delete_num = 0;
	
	$wx_dept_arr = array();
	$wx_user_arr = array();
	
	$db_dept_arr = array();
	$sql = "SELECT id FROM user_group";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			$db_dept_arr[] = $row[0];
		}				
	}
	
	$db_user_arr = array();
	$sql = "SELECT userid FROM user WHERE id!='999'";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			$db_user_arr[] = $row[0];
		}				
	}
	
	//获取部门列表
	$url = "https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=".$_SESSION['wx_token'];
	$result = https_post($url);
	$result = json_decode($result,true);
	if(array_key_exists("errcode",$result)){
		if($result['errcode']!=0){
			$_SESSION['wx_token'] = "";
			$arr = array(
				'success'=>0,
				'error'=>$result['errcode'].":".$result['errmsg']
			);
			echo json_encode($arr);
			exit;
		}
		else{
			$dept_arr = $result['department'];
			foreach($dept_arr as $dept){
				$dept_import_num++;
				
				$dept_id = $dept['id'];
				$dept_name = $dept['name'];
				$dept_parentid = $dept['parentid'];
				$dept_order = $dept['order'];
				$dept_route = build_route($dept_parentid,$dept_id,"user_group");
				
				$wx_dept_arr[] = $dept_id;
				
				if(in_array($dept_id, $db_dept_arr)){//更新部门
					$sql = "UPDATE user_group 
							SET parent_id='$dept_parentid',
								name='$dept_name',
								sort_order='$dept_order',
								route='$dept_route' 
							WHERE id='$dept_id'";
					$dept_update_num++;
				}
				else{//添加部门
					$sql = "INSERT INTO user_group(
								id,
								parent_id,
								name,
								sort_order,
								route,
								created_by,
								created_time
							) VALUES(
								'$dept_id',
								'$dept_parentid',
								'$dept_name',
								'$dept_order',
								'$dept_route',
								'".$_SESSION['u_id']."',
								'".time()."'
							)";
					$dept_insert_num++;
				}				
				if(mysql_query($sql)){					
					//获取部门成员详情
					$url = "https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=".$_SESSION['wx_token']."&department_id=".$dept_id."&fetch_child=1";
					$result = https_post($url);
					$result = json_decode($result,true);
					if(array_key_exists("errcode",$result)){
						if($result['errcode']!=0){
							$_SESSION['wx_token'] = "";
							$arr = array(
								'success'=>0,
								'error'=>$result['errcode'].":".$result['errmsg']
							);
							echo json_encode($arr);
							exit;
						}
						else{							
							$user_arr = $result['userlist'];
							foreach($user_arr as $user){
								$user_import_num++;								
								
								$userid = $user['userid'];
								$wx_user_arr[] = $userid;
								
								$name = $user['name'];
								$dept_list = $user['department'];
								$department = "";
								foreach($dept_list as $dept){
									if($department!="")$department .= "|";
									$department .= $dept;
								}
								$sort_order = $user['order'];
								$position = $user['position'];
								$mobile = $user['mobile'];
								$gender = $user['gender'];
								$email = $user['email'];
								$avatar = $user['avatar'];
								$status = $user['status'];
								
								$insert_key_str = "";
								$insert_val_str = "";
								$update_val_str = "";
								foreach($GLOBALS['user_fields'] as $key){
									if($insert_key_str!="")$insert_key_str .= ",";
									$insert_key_str .= $key;
									
									if($key=="id"){
										$sql = "SELECT MAX(id) FROM user WHERE id!=999";
										${$key} = mysql_result(mysql_query($sql),0)+1;
									}
									if($key=="password"){
										${$key} = sha1("111111".SALT);
									}
									
									if($insert_val_str!="")$insert_val_str .= ",";
									$insert_val_str .= "'".${$key}."'";
								}
								foreach($GLOBALS['user_fields_wx'] as $key){
									if($insert_key_str!="")$insert_key_str .= ",";
									$insert_key_str .= $key;
									
									if($insert_val_str!="")$insert_val_str .= ",";
									$insert_val_str .= "'".${$key}."'";
									
									if($update_val_str!="")$update_val_str .= ",";
									$update_val_str .= $key."='".${$key}."'";
								}
								
								$sql = "SELECT COUNT(*) FROM user WHERE userid='$userid'";
								$has_user = mysql_result(mysql_query($sql),0);
								if($has_user==0){
									$sql = "INSERT INTO user(
												$insert_key_str
											) VALUES(
												$insert_val_str
											)";
									$user_insert_num++;
								}
								else{
									$sql = "UPDATE user SET $update_val_str WHERE userid='$userid'";
									$user_update_num++;
								}
								if(!mysql_query($sql)){
									$arr = array(
										'success'=>0,
										'error'=>"保存成员信息出错：".mysql_error()
									);
									echo json_encode($arr);
									exit;
								}
							}
						}
					}
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"保存部门信息出错：".mysql_error()
					);
					echo json_encode($arr);
					exit;
				}
			}
			
			$db_dept_arr = array_unique($db_dept_arr);
			$wx_dept_arr = array_unique($wx_dept_arr);
			foreach($db_dept_arr as $db_dept){
				if(!in_array($db_dept,$wx_dept_arr)){
					$dept_delete_num++;
					$sql = "DELETE FROM user_group WHERE id='$db_dept'";
					if(!mysql_query($sql)){
						$arr = array(
							'success'=>0,
							'error'=>"删除部门信息出错：".mysql_error()
						);
						echo json_encode($arr);
						exit;
					}
				}
			}
			
			$db_user_arr = array_unique($db_user_arr);
			$wx_user_arr = array_unique($wx_user_arr);
			foreach($db_user_arr as $db_user){
				if(!in_array($db_user,$wx_user_arr)){
					$user_delete_num++;
					$sql = "DELETE FROM user WHERE userid='$db_user'";
					if(!mysql_query($sql)){
						$arr = array(
							'success'=>0,
							'error'=>"删除成员信息出错：".mysql_error()
						);
						echo json_encode($arr);
						exit;
					}
				}
			}
			
			include_once("sync_user.php");
			
			$arr = array(
				'success'=>1,
				'dept_import_num'=>$dept_import_num,
				'dept_insert_num'=>$dept_insert_num,
				'dept_update_num'=>$dept_update_num,
				'dept_delete_num'=>$dept_delete_num,
				'user_import_num'=>$user_import_num,
				'user_insert_num'=>$user_insert_num,
				'user_update_num'=>$user_update_num,
				'user_delete_num'=>$user_delete_num,
			);
			echo json_encode($arr);
			exit;
		}		
	}
}