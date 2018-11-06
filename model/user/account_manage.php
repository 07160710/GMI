<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if(isset($_POST['action'])){
	if($_SESSION['wx_token']==""){
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

if($_POST['action']=="move"){
	$object = mysql_escape_string($_POST['object']);
	$id = mysql_escape_string($_POST['id']);
	$parent_id = mysql_escape_string($_POST['parent_id']);
	$user_info = get_user_info($id);
	
	if($object=="user"){
		$sql = "UPDATE user SET department='$parent_id' WHERE id='$id'";
		if(mysql_query($sql)){
			$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token='.$_SESSION['wx_token'];
			$data = '{
					   "userid": "'.$user_info['userid'].'",
					   "department": ['.$parent_id.']
					}';
			$result = https_post($url,$data);
			$result = json_decode($result,true);
			if($result['errcode']!=0){			
				$arr = array(
					'success'=>0,
					'access_token'=>$_SESSION['wx_token'],
					'error'=>$result['errcode'].":".$result['errmsg']
				);
				$_SESSION['wx_token'] = "";
				echo json_encode($arr);
				exit;
			}
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"更新用户所属群组出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
		
		$arr = array(
			'success'=>1,
			'g_id'=>$parent_id,
		);
		echo json_encode($arr);
		exit;
	}
}

if($_POST['action']=="sort"){
	if(isset($_POST['sort'])){
		$i = 100;
		foreach($_POST['sort'] as $n_id){
			$n_id = mysql_escape_string($n_id);
			
			$sql = "UPDATE user_group SET sort_order='$i' WHERE id='$n_id'";
			if(mysql_query($sql)){
				$url = 'https://qyapi.weixin.qq.com/cgi-bin/department/update?access_token='.$_SESSION['wx_token'];
				$data = '{
						   "id": '.$n_id.',
						   "order": '.$i.'
						}';
				$result = https_post($url,$data);
				$result = json_decode($result,true);
				if($result['errcode']!=0){			
					$arr = array(
						'success'=>0,
						'access_token'=>$_SESSION['wx_token'],
						'error'=>$result['errcode'].":".$result['errmsg']
					);
					$_SESSION['wx_token'] = "";
					echo json_encode($arr);
					exit;
				}
				
				$i--;
			}
			else{
				$arr = array(
					'success'=>0,
					'error'=>"更新群组排序出错：".mysql_error()
				);
				echo json_encode($arr);
				exit;
			}
		}
		
		$arr = array(
			'success'=>1
		);
		echo json_encode($arr);
		exit;
	}
}

if($_POST['action']=="save_group"){
	$type = mysql_escape_string($_POST['type']);
	$parent_id = mysql_escape_string($_POST['parent_id']);
	$g_name = mysql_escape_string($_POST['group_name']);
	
	if($type=="add"){
		$sql = "SELECT MIN(sort_order) FROM user_group WHERE parent_id='$parent_id'";
		$min_order = mysql_result(mysql_query($sql),0);
		$new_order = ($min_order>0)?$min_order-1:100;
		
		$new_id = get_new_id('user_group');
		$route = build_route($parent_id,$new_id,"user_group");
		
		$sql = "INSERT INTO user_group(
					id,
					parent_id,
					name,
					sort_order,
					route,
					created_by,
					created_time
				) VALUES(
					'$new_id',
					'$parent_id',
					'$g_name',
					'$new_order',
					'$route',
					'".$_SESSION['u_id']."',
					'".time()."'
				)";
		if(mysql_query($sql)){
			$url = 'https://qyapi.weixin.qq.com/cgi-bin/department/create?access_token='.$_SESSION['wx_token'];
			$data = '{
						"name": "'.$g_name.'",
						"parentid": '.$parent_id.',						
						"order": '.$new_order.',
						"id": '.$new_id.'
					}';		
			$result = https_post($url,$data);
			$result = json_decode($result,true);
			if($result['errcode']!=0){			
				$arr = array(
					'success'=>0,
					'access_token'=>$_SESSION['wx_token'],
					'error'=>$result['errcode'].":".$result['errmsg']
				);
				$_SESSION['wx_token'] = "";
				echo json_encode($arr);
				exit;
			}
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"插入群组出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	else{
		$g_id = mysql_escape_string($_POST['g_id']);
		
		$auth_ctrl_arr = [];
		foreach($GLOBALS['user_auth'] as $auth){
			${$auth} = mysql_escape_string($_POST[$auth]);
			$auth_ctrl_arr[$auth] = ${$auth};
		}
		$auth_ctrl = json_encode($auth_ctrl_arr, true);
		
		$branch_arr = [];
		foreach($_POST['branch'] as $branch){
			$branch_arr[] = $branch;
		}
		$branch_str = json_encode($branch_arr, true);
		
		$role_str = "";
		foreach($_POST['role'] as $role){
			if($role_str!="")$role_str .= ",";
			$role_str .= $role;
		}
		
		function update_group_user($g_id){
			global $auth_ctrl;
			global $branch_str;
			global $role_str;
			
			$sql = "SELECT id FROM user WHERE department='$g_id'";
			$get_user = mysql_query($sql);
			if(mysql_num_rows($get_user)>0){
				while($row = mysql_fetch_array($get_user)){
					$u_id = $row[0];
					$sql = "UPDATE user SET auth_ctrl='$auth_ctrl',branch='$branch_str',role='$role_str' WHERE id='$u_id'";
					if(!mysql_query($sql)){
						$arr = array(
							'success'=>0,
							'error'=>"更新成员权限出错：".mysql_error()
						);
						echo json_encode($arr);
						exit;
					}
				}
			}
		}
		
		function get_dept($parent_id){
			$sql = "SELECT id FROM user_group WHERE parent_id='$parent_id'";
			$get_dept = mysql_query($sql);
			if(mysql_num_rows($get_dept)>0){
				while($row = mysql_fetch_array($get_dept)){
					$g_id = $row[0];
					if(has_child($g_id, "user_group"))get_dept($g_id);
					else update_group_user($g_id);
				}
			}
		}
		
		if(has_child($g_id, "user_group"))get_dept($g_id);
		else update_group_user($g_id);
		
		$route = build_route($parent_id,$g_id,"user_group");
		$sql = "UPDATE user_group SET name='$g_name',route='$route',auth_ctrl='$auth_ctrl',branch='$branch_str',role='$role_str' WHERE id='$g_id'";
		if(mysql_query($sql)){
			$url = 'https://qyapi.weixin.qq.com/cgi-bin/department/update?access_token='.$_SESSION['wx_token'];
			$data = '{	
						"id": '.$g_id.',
						"name": "'.$g_name.'"
					}';
			$result = https_post($url,$data);
			$result = json_decode($result,true);
			if($result['errcode']!=0){			
				$arr = array(
					'success'=>0,
					'access_token'=>$_SESSION['wx_token'],
					'error'=>$result['errcode'].":".$result['errmsg']
				);
				$_SESSION['wx_token'] = "";
				echo json_encode($arr);
				exit;
			}
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"更新群组出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	
	$arr = array(
		'success'=>1
	);
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="save_account"){
	$acc_id = mysql_escape_string($_POST['acc_id']);
	$name = mysql_escape_string($_POST['name']);
	$name_en = mysql_escape_string($_POST['name_en']);
	$mobile = mysql_escape_string($_POST['mobile']);
	$email = mysql_escape_string($_POST['email']);
	$department = mysql_escape_string($_POST['department']);
	$is_leader = 0;
	if($_POST['is_leader']=="on")$is_leader = 1;
	
	$position = mysql_escape_string($_POST['position']);
	$gender = mysql_escape_string($_POST['gender']);
	$level = mysql_escape_string($_POST['level']);
	$status = mysql_escape_string($_POST['active']);
	$intro = mysql_escape_string($_POST['intro']);
	
	if($name==""){
		$arr = array(
			'success'=>0,
			'error'=>"请输入成员姓名！"
		);					
		echo json_encode($arr);
		exit;
	}
	if($email!="" && validate_email($email)==false){
		$arr = array(
			'success'=>0,
			'error'=>"邮箱格式错误！"
		);					
		echo json_encode($arr);
		exit;
	}
	if($_POST['new_password']!=""){
		if(validate_password($_POST['new_password'])==true){
			if($_POST['cfm_password']!=""){
				if($_POST['new_password']==$_POST['cfm_password']){
					$raw_new_pass = mysql_escape_string($_POST['new_password']);
					$new_pass = sha1($raw_new_pass.SALT);
					$new_pass_str = "password='$new_pass',";
				}
				else{
					$arr = array(
						'success'=>0,
						'error'=>"确认密码与新密码不一致！"
					);					
					echo json_encode($arr);
					exit;
				}
			}
			else{
				$arr = array(
					'success'=>0,
					'error'=>"请确认新密码！"
				);					
				echo json_encode($arr);
				exit;
			}
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"密码格式不符合规范！"
			);					
			echo json_encode($arr);
			exit;
		}
	}
	
	$auth_ctrl_arr = [];
	foreach($GLOBALS['user_auth'] as $auth){
		${$auth} = mysql_escape_string($_POST[$auth]);
		$auth_ctrl_arr[$auth] = ${$auth};
	}
	$auth_ctrl = json_encode($auth_ctrl_arr, true);
	
	$branch_arr = [];
	foreach($_POST['branch'] as $branch){
		$branch_arr[] = $branch;
	}
	$branch_str = json_encode($branch_arr, true);
	
	$role_str = "";
	foreach($_POST['role'] as $role){
		if($role_str!="")$role_str .= ",";
		$role_str .= $role;
	}
	
	if($acc_id==""){
		$acc_id = get_new_id("user");
		
		$sql = "INSERT INTO user(
					id,
					userid,
					name,
					name_en,
					mobile,
					email,
					password,
					department,
					is_leader,
					position,
					gender,
					level,
					status
				) VALUES(
					'$acc_id',
					'$mobile',
					'$name',
					'$name_en',
					'$mobile',
					'$email',
					'$new_pass',
					'$department',
					'$is_leader',
					'$position',
					'$gender',
					'$level',
					'$status'
				)";
		if(mysql_query($sql)){
			$user_info = get_user_info($acc_id);
			$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token='.$_SESSION['wx_token'];			
			$data = '{
					   "userid": "'.$user_info['userid'].'",
					   "name": "'.$name.'",
					   "english_name": "'.$name_en.'",
					   "department": ['.$department.'],
					   "position" : "'.$position.'",
					   "mobile": "'.$mobile.'",
					   "gender": "'.$gender.'",
					   "email": "'.$email.'",
					   "isleader": '.$is_leader.',
					   "enable": '.$status.'
					}';		
			$result = https_post($url,$data);
			$result = json_decode($result,true);
			if($result['errcode']!=0){			
				$arr = array(
					'success'=>0,
					'access_token'=>$_SESSION['wx_token'],
					'error'=>$result['errcode'].":".$result['errmsg']
				);
				$_SESSION['wx_token'] = "";
				echo json_encode($arr);
				exit;
			}
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"保存成员信息出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	else{
		$sql = "UPDATE user 
				SET name='$name',
					name_en='$name_en',
					mobile='$mobile',
					email='$email',
					$new_pass_str
					department='$department',
					is_leader='$is_leader',
					position='$position',
					gender='$gender',
					level='$level',
					intro='$intro',
					auth_ctrl='$auth_ctrl',
					branch='$branch_str',
					role='$role_str',
					status='$status' 
				WHERE id='$acc_id'";
		if(mysql_query($sql)){
			$user_info = get_user_info($acc_id);
			
			//获取部门成员详情
			$wxuser_arr = array();
			$url = "https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=".$_SESSION['wx_token']."&department_id=".$department."&fetch_child=1";
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
						$wxuserid = $user['userid'];
						$wxuser_arr[] = $wxuserid;
					}
				}
			}
			
			if(in_array($user_info['userid'],$wxuser_arr)){//更新
				$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token='.$_SESSION['wx_token'];
			}
			else{//添加
				$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token='.$_SESSION['wx_token'];
			}
			$data = '{
					   "userid": "'.$user_info['userid'].'",
					   "name": "'.$name.'",
					   "english_name": "'.$name_en.'",
					   "department": ['.$department.'],
					   "isleader": '.$is_leader.',
					   "position" : "'.$position.'",
					   "mobile": "'.$mobile.'",
					   "gender": "'.$gender.'",
					   "email": "'.$email.'",
					   "isleader": '.$is_leader.',
					   "enable": '.$status.'
					}';
			$result = https_post($url,$data);
			$result = json_decode($result,true);
			if($result['errcode']!=0){			
				$arr = array(
					'success'=>0,
					'access_token'=>$_SESSION['wx_token'],
					'error'=>$result['errcode'].":".$result['errmsg']
				);
				$_SESSION['wx_token'] = "";
				echo json_encode($arr);
				exit;
			}
		}
		else{
			$arr = array(
				'success'=>0,
				'error'=>"保存成员信息出错：".mysql_error()
			);
			echo json_encode($arr);
			exit;
		}
	}
	
	$arr = array(
		'success'=>1,
		'acc_id'=>$acc_id
	);
	echo json_encode($arr);
	exit;
}

function delete_user($acc_id){
	$error = "";	
	$user_info = get_user_info($acc_id);
	
	$sql = "DELETE FROM user WHERE id='$acc_id'";
	if(mysql_query($sql)){
		$sql = "UPDATE project_assign SET is_curr=0 WHERE u_id='$acc_id'";
		mysql_query($sql);
		
		$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token='.$_SESSION['wx_token'].'&userid='.$user_info['userid'];
		$result = https_post($url);
		$result = json_decode($result,true);
	}
	else{
		$error = mysql_error();
	}
	return $error;
}

if($_POST['action']=="delete_user"){
	$acc_id = mysql_escape_string($_POST['acc_id']);
	
	$sql = "SELECT department FROM user WHERE id='$acc_id'";
	$g_id = mysql_result(mysql_query($sql),0);
	
	$error = delete_user($acc_id);
	if($error!=""){
		$arr = array(
			'success'=>0,
			'error'=>"删除成员出错：".$error
		);
	}
	else{
		$arr = array(
			'success'=>1,
			'g_id'=>$g_id
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="delete_group"){
	$g_id = mysql_escape_string($_POST['group_id']);
	
	$sql = "SELECT parent_id FROM user_group WHERE id='$g_id'";
	$parent_id = mysql_result(mysql_query($sql),0);
	
	$user_arr = [];
	$sql = "SELECT id FROM user WHERE department='$g_id'";
	$get_user = mysql_query($sql);
	if(mysql_num_rows($get_user)>0){
		while($row = mysql_fetch_array($get_user)){
			$user_arr[] = $row[0];
		}
	}
	
	$sql = "DELETE FROM user_group WHERE id='$g_id'";
	if(mysql_query($sql)){
		foreach($user_arr as $acc_id){
			$error = delete_user($acc_id);
			if($error!=""){
				$arr = array(
					'success'=>0,
					'error'=>"删除成员出错：".$error
				);
				echo json_encode($arr);
				exit;
			}
		}
		
		$url = 'https://qyapi.weixin.qq.com/cgi-bin/department/delete?access_token='.$_SESSION['wx_token'].'&id='.$g_id;
		$result = https_post($url);
		$result = json_decode($result,true);
		$arr = array(
			'success'=>1,
			'g_id'=>$parent_id,
		);
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"删除群组出错：".mysql_error()
		);
	}
	echo json_encode($arr);
	exit;
}

if($_POST['action']=="get_avatar"){
	$userid = mysql_escape_string($_POST['userid']);
	
	$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token='.$_SESSION['wx_token'].'&userid='.$userid;
	$result = https_post($url);
	$result = json_decode($result,true);
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
		$avatar = $result['avatar'];
		$sql = "UPDATE user SET avatar='$avatar' WHERE userid='$userid'";
		if(!mysql_query($sql)){
			$arr = array(
				'success'=>0,
				'error'=>"更新用户头像失败：".mysql_error()
			);
		}
		else{			
			$arr = array(
				'success'=>1,
				'avatar'=>$avatar
			);
		}
		echo json_encode($arr);
		exit;
	}
}