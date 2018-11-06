<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

$alert_msg = "";
if($_POST['ctrl']=="edit_profile"){
	if($_POST['username']==""){
		$arr = array(
			'success'=>0,
			'error'=>"请输入用户名。"
		);
		echo json_encode($arr);
		exit;
	}
	$name = mysql_escape_string($_POST['username']);
	
	if($_POST['email']==""){
		$arr = array(
			'success'=>0,
			'error'=>"请输入电子邮箱。"
		);
		echo json_encode($arr);
		exit;
	}
	$email = mysql_escape_string($_POST['email']);
	
	if(!validate_email($email)){
		$arr = array(
			'success'=>0,
			'error'=>"电子邮箱格式不正确。"
		);
		echo json_encode($arr);
		exit;
	}
	
	$sql = "SELECT COUNT(*) FROM user WHERE email='$email' AND id!='".$_SESSION['u_id']."'";
	$has_email = mysql_result(mysql_query($sql),0);
	if($has_email!=0){
		$arr = array(
			'success'=>0,
			'error'=>"此邮箱已存在，请输入另一个。"
		);
		echo json_encode($arr);
		exit;
	}
	
	$position = mysql_escape_string($_POST['position']);
	
	$sql = "UPDATE user 
			SET 
				name = '$name',
				position = '$position',
				email = '$email' 
			WHERE id = '".$_SESSION['u_id']."'";
	if(mysql_query($sql)){
		$_SESSION['u_name'] = $name;
		include_once("sync_user.php");
		
		$arr = array(
			'action'=>$_POST['ctrl'],
			'success'=>1
		);
	}
	else{
		$arr = array(
			'success'=>0,
			'error'=>"保存资料出错：".mysql_error()
		);
	}	
	echo json_encode($arr);
	exit;
}

if($_POST['ctrl']=="reset_pass"){	
    if($_POST['old_password']!=""){
	
		if($_POST['new_password']!="" && $_POST['cfm_password']!=""){
		
			if(validate_password($_POST['new_password'])==true){
				
				if($_POST['new_password']==$_POST['cfm_password']){
				
					$raw_old_pass = mysql_escape_string($_POST['old_password']);
					$old_pass = sha1($raw_old_pass.SALT);
					
					$sql = "SELECT * FROM user WHERE id = '".$_SESSION['u_id']."' AND password = '$old_pass'";					
					$check_pass = mysql_query($sql);
					if(mysql_num_rows($check_pass)>0){
						$raw_new_pass = mysql_escape_string($_POST['new_password']);
						$new_pass = sha1($raw_new_pass.SALT);
				
						$sql = "UPDATE user SET password = '$new_pass' WHERE id = '".$_SESSION['u_id']."'";
						if(mysql_query($sql)){
							include_once("sync_user.php");
							
							$arr = array(
								'action'=>$_POST['ctrl'],
								'success'=>1
							);
							echo json_encode($arr);
							exit;
						}
						else{
							$alert_msg = "更新用户密码出错: ".mysql_error();
						}
					}
					else{
						$alert_msg = "旧密码不正确，请重试。";
					}
				}
				else{
					$alert_msg = "新密码与确认密码不一致。";
				}
			}
			else{
				$alert_msg = "您的密码不符合设定规则。";
			}
		}
		else{
			$alert_msg = "请输入新密码并确认密码。";
		}
	}
	else{
		$alert_msg = "请输入旧密码。";
	}
	$arr = array(
		'success'=>0,
		'error'=>$alert_msg
	);
	echo json_encode($arr);
	exit;
}

if($_POST['ctrl']=="site_setting"){
	if(isset($_POST['mailbox_type'])){
		$mailbox_type = mysql_escape_string($_POST['mailbox_type']);
		$qywx_appid = mysql_escape_string($_POST['qywx_appid']);
		$qywx_agentid = mysql_escape_string($_POST['qywx_agentid']);
		$qywx_appsecret = mysql_escape_string($_POST['qywx_appsecret']);
		
		if($_POST['smtpsender']!=""){
			$smtpsender = mysql_escape_string($_POST['smtpsender']);
			$smtpserver = mysql_escape_string($_POST['smtpserver']);
			$smtpserverport = mysql_escape_string($_POST['smtpserverport']);
			
			$smtpmail = mysql_escape_string($_POST['smtpmail']);
			if($smtpmail!="" && validate_email($smtpmail)===false){
				$arr = array(
					'action'=>$_POST['ctrl'],
					'success'=>0,
					'error'=>"电子邮箱格式不正确。"
				);
				echo json_encode($arr);
				exit;
			}
			
			$smtpuser = mysql_escape_string($_POST['smtpuser']);
			$smtppass = mysql_escape_string($_POST['smtppass']);
			$update_pass_str = "";
			if($smtppass!="")$update_pass_str = ",smtppass='$smtppass'";
			
			$sql = "UPDATE site_table 
					SET 
						qywx_appid='$qywx_appid',
						qywx_agentid='$qywx_agentid',
						qywx_appsecret='$qywx_appsecret',
						mailbox_type='$mailbox_type',
						smtpsender='$smtpsender',
						smtpserver='$smtpserver',
						smtpserverport='$smtpserverport',
						smtpmail='$smtpmail',
						smtpuser='$smtpuser'
						$update_pass_str 
					WHERE id='".$_SESSION['site_id']."'";
			if(mysql_query($sql)){					
				$arr = array(
					'action'=>$_POST['ctrl'],
					'success'=>1
				);
				echo json_encode($arr);
				exit;
			}
			else{
				$alert_msg = "Error: ".mysql_error();
			}
		}
		else{
			$alert_msg = "请输入发件人姓名。";
		}
	}
	else{
		$alert_msg = "请选择邮箱类型。";
	}
	$arr = array(
		'action'=>$_POST['ctrl'],
		'success'=>0,
		'error'=>$alert_msg
	);
	echo json_encode($arr);
	exit;
}
?>