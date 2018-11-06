<?php
require("include/conn.php");
require("public_param.php");
include("function.php");

if($_SESSION['u_id']!=""){
	if(isset($_REQUEST['logout'])){
		$sql = "UPDATE user 
				SET 
					last_login = login_time,
					last_logout = '".time()."' 
				WHERE id = '".$_SESSION['u_id']."'";
		if(mysql_query($sql)){
			session_destroy();		
			
			if($_REQUEST['logout']==1){
				setcookie("response","logout");
			}
			else if($_REQUEST['logout']==2){
				setcookie("response","timeout");
			}
			else{
				setcookie("response","unknown");
			}
			header("Location:./");
			exit;
		}
	}
	else{
		$raw_referer = (strpos($_COOKIE['referer'],"manage_login")!==false)?"":$_COOKIE['referer'];
		header("Location:".$raw_referer);
		exit;
	}
}
else if(isset($_GET['code'])){
	$code = $_GET['code'];
	
	$url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$GLOBALS['zhiku_appid'].'&corpsecret='.$GLOBALS['zhiku_appsecret'];
	$result = https_post($url);
	$result = json_decode($result,true);
	if($result['errcode']!=0){	
		echo '<h1>错误：</h1>'.$result['errcode'];
		echo '<br/><h2>错误信息：</h2>'.$result['errmsg'];
		exit;
	}
	else{
		$_SESSION['wechat_token'] = $result['access_token'];
	}
	
	$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token='.$_SESSION['wechat_token'].'&code='.$code;
	$result = https_post($url);
	$result = json_decode($result,true);
	if($result['errcode']!=0){	
		echo '<h1>错误：</h1>'.$result['errcode'];
		echo '<br/><h2>错误信息：</h2>'.$result['errmsg'];
		exit;
	}
	else{
		$userid = $result['UserId'];
		
		$url = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token='.$_SESSION['wechat_token'].'&userid='.$userid;
		$result = https_post($url);
		$result = json_decode($result,true);
		if($result['errcode']!=0){	
			echo '<h1>错误：</h1>'.$result['errcode'].'<br/>';
			echo '<h2>错误信息：</h2>'.$result['errmsg'];
			exit;
		}
		else{			
			$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
			$default_lang = 0;
			if(preg_match("/zh-c/i", $lang))$default_lang = 1;
			else if(preg_match("/zh/i", $lang))$default_lang = 1;
			else $default_lang = 0;
			
			$sql = "UPDATE user 
					SET login_time='$curr_time' 
					WHERE userid='$userid'";
			if(mysql_query($sql)){
				$sql = "SELECT * FROM user WHERE userid='".$userid."'";
				$stmt = mysql_query($sql);
				if(mysql_num_rows($stmt)>0){
					$row = mysql_fetch_array($stmt);
					$_SESSION['u_id'] = $row['id'];
					$_SESSION['u_name'] = $row['name'];				
					$_SESSION['u_img'] = $row['avatar'];
					$_SESSION['level'] = $row['level'];
					$_SESSION['role'] = $row['role'];
					$_SESSION['department'] = $row['department'];
					
					$auth_ctrl = $row['auth_ctrl'];
					$auth_ctrl_arr = json_decode($auth_ctrl, true);
					foreach($GLOBALS['user_auth'] as $auth){
						$_SESSION[$auth] = $auth_ctrl_arr[$auth];
						
						if($_SESSION['level']==3){
							$_SESSION[$auth] = 2;
						}
					}
					
					$raw_referer = (strpos($_COOKIE['referer'],"manage_login")!==false)?"":$_COOKIE['referer'];
					$url_arr = parse_url($raw_referer);
					$referer = $url_arr['path'];
					$referer_arr = explode("/",$referer);
					$referer_arr = array_reverse($referer_arr);
					$referer = $referer_arr[0];
					
					if($referer!=""){
						header("Location:".$referer);
						exit;
					}
					else{
						header("Location:home.php");
						exit;
					}
				}
			}
			else{
				$error = "保存用户信息出错：".mysql_error();
				setcookie("response",$error);
				header("Location:.");
				exit;
			}
			close_conn("zkwf");
		}
	}
}
else if($_POST['action']=="login"){
	if(!isset($_SESSION['u_id']) || $_SESSION['u_id']==""){//log in
		$login_account = mysql_escape_string($_POST['account']);
		$login_password = mysql_escape_string($_POST['password']);
		
		$encrypted_pass = sha1($login_password.SALT);
		
		$sql = "SELECT * 
				FROM user 
				WHERE (mobile='$login_account' OR email='$login_account') AND password = '$encrypted_pass'";
		$get_user = mysql_query($sql);	
		if(mysql_num_rows($get_user) > 0){
			$row = mysql_fetch_array($get_user);
			$status = $row['status'];
			if($status==1){
				$_SESSION['u_id'] = $row['id'];
				$_SESSION['u_name'] = $row['name'];				
				$_SESSION['u_img'] = $row['avatar'];
				$_SESSION['level'] = $row['level'];
				$_SESSION['role'] = $row['role'];
				$_SESSION['department'] = $row['department'];
				
				$auth_ctrl = $row['auth_ctrl'];
				$auth_ctrl_arr = json_decode($auth_ctrl, true);
				foreach($GLOBALS['user_auth'] as $auth){
					$_SESSION[$auth] = $auth_ctrl_arr[$auth];
					
					if($_SESSION['level']==3){
						$_SESSION[$auth] = 2;
					}
				}
				
				$raw_referer = (strpos($_COOKIE['referer'],"manage_login")!==false)?"":$_COOKIE['referer'];
				$url_arr = parse_url($raw_referer);
				$referer = $url_arr['path'];
				$referer_arr = explode("/",$referer);
				$referer_arr = array_reverse($referer_arr);
				$referer = $referer_arr[0];
				
				$sql = "UPDATE user SET login_time='".time()."' WHERE id='".$_SESSION['u_id']."'";
				if(mysql_query($sql)){
					if($referer!=""){
						$arr = array(
							'success'=>1,
							'referer'=>$referer
						);
					}
					else{
						$arr = array(
							'success'=>1,
							'referer'=>'home.php'
						);
					}
				}
			}
			else{
				if($status==2)$error = "您的账号已被禁用，请联系系统管理员。";
				if($status==0 || $status==4)$error = "您的账号尚未激活，请联系系统管理员。";
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>$error
				);
			}
		}
		else{
			$arr = array(
				'action'=>$_POST['action'],
				'success'=>0,
				'error'=>'电邮/手机与密码不匹配，请重新输入。'
			);
		}
		close_conn("zkwf");
		echo json_encode($arr);
		exit;
	}
}
else if($_POST['action']=="reset_password"){
	if($_POST['email']!=""){
		$mail_to = mysql_escape_string($_POST['email']);
		
		if(validate_email($mail_to)==true){
			$sql = "SELECT * FROM user WHERE email = '$mail_to'";
			$check_user = mysql_query($sql);			
			if(mysql_num_rows($check_user)>0){
				$cu_row = mysql_fetch_array($check_user);
				$raw_pass = generate_password();
				$new_pass = sha1($raw_pass.SALT);
				
				$reset_pass_query = "UPDATE user SET password = '$new_pass' WHERE email = '$mail_to'";
				if(mysql_query($reset_pass_query)){
					require_once("include/smtp.php");
					include_once("include/mail_setting.php");
					
					$smtpemailto = $mail_to;
					$mailproject = 	"您在".translate_str(SITE_NAME)."的密码已被重设";
					$mailbody = "<p>尊敬的".$cu_row['username'].", </p>" .
								"<p>您的密码已被重设为 <b style='color:#008bcc;'>" . $raw_pass . "</b></p>" .
								"<p>请妥善保管。</p>" .
								translate_str(SITE_NAME);
					
					$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
					//$smtp->debug = true;
					if ($smtp->sendmail($smtpemailto, $smtpusername, $smtpusermail, $mailproject, $mailbody, $mailtype)){
						$arr = array(
							'action'=>$_POST['action'],
							'success'=>1
						);
					}
					else{
						$arr = array(
							'action'=>$_POST['action'],
							'success'=>0,
							'error'=>"发送邮件失败，请联系管理员。"
						);
					}
				}
			}
			else{
				$arr = array(
					'action'=>$_POST['action'],
					'success'=>0,
					'error'=>"抱歉，此邮箱地址非注册邮箱。"
				);
			}
			close_conn("zkwf");
		}
		else{
			$arr = array(
				'action'=>$_POST['action'],
				'success'=>0,
				'error'=>"电子邮箱格式有误，请重新输入。"
			);
		}
	}
	else{
		$arr = array(
			'action'=>$_POST['action'],
			'success'=>0,
			'error'=>"请输入电子邮箱。"
		);
	}
	
	echo json_encode($arr);
	exit;
}
else{
	session_destroy();
	setcookie("response","logout");
	header("Location:./");
	exit;
}
?>