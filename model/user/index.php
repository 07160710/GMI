<?php
include("public_param.php");
require("function.php");
if($_SESSION['u_id']!=""){
	header("Location:manage_login.php");
	exit;
}

$url_arr = parse_url($_SERVER['REQUEST_URI']);
$params_arr = explode("&",$url_arr['query']);
foreach($params_arr as $param){
	$param_arr = explode("=",$param);
	${$param_arr[0]} = mysql_escape_string($param_arr[1]);
}

if(isset($mode)){
	if($_COOKIE['mode']==""){
		print "<script>document.cookie='mode=$mode';</script>";
	}
	print "<script>location.href='".$url_arr['path']."';</script>";
	exit;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
	<script src="js/basic_fn.js" type="text/javascript"></script>
	<title><?php print SITE_NAME;?></title>
</head>

<body>
<div id="main_content" style="margin:0;background:transparent;">	
	<?php if($_COOKIE['mode']==""){ ?>
	<table class="login_container wx" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<div class="login_logo_holder">
					<img src="images/logo.png">
				</div>
				<table border="0" cellpadding="0" cellspacing="0" class="login_holder">
					<tr>
						<td height="20">
							<!--<h1><?php print SITE_NAME;?></h1>-->
						</td>
					</tr>
					<tr>
						<td align="center" colspan="2">
							<div id="qrcode_holder"></div>
						</td>
					</tr>
				</table>
	<?php } ?>
	<?php if($_COOKIE['mode']=="preview"){ ?>
	<table class="login_container reg" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<div class="content_holder">
					<div class="login_logo_holder">
						<img src="images/logo.png">
					</div>
					<table border="0" cellpadding="0" cellspacing="10" class="login_holder">
						<tr>
							<td class="logo_holder" colspan="2">
								<h1><?php print SITE_NAME;?></h1>
							</td>
						</tr>
						<tr>
							<td class="title">
								手机/邮箱
							</td>
							<td>
								<input name="login_account" type="text" id="login_account" class="login_field">
							</td>
						</tr>
						<tr>
							<td class="title">
								密码
							</td>
							<td>
								<input name="login_password" type="password" id="login_password" class="login_field" onkeydown="if(event.keyCode==13)manage_process('login');">
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<button style="submit" class="ctrl_btn active login" id="btn_login">
									登录
								</button>
								<div class="clear"></div>
								<a id="btn_forgot">
									忘记密码？
								</a>
							</td>
						</tr>	
					</table>

					<table border="0" cellpadding="0" cellspacing="10" class="reset_holder">
						<tr>
							<td colspan="2" class="logo_holder">
								<h1>
									重设密码
								</h1>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="line-height:20px;">
								请输入您的注册邮箱，点击“重设密码”，新的密码将会发送到您的邮箱。
							</td>
						</tr>
						<tr>
							<td class="title">
								电子邮箱
							</td>
							<td>
								<input name="reset_email" type="text" id="reset_email" class="reset_field">
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<button class="ctrl_btn active reset" id="btn_reset">
									重设密码
								</button>
								<div class="clear"></div>
								<a id="btn_back" style="color:#999;">&lt;返回</a>
							</td>
						</tr>
					</table>
				</div>
	<?php } ?>
			</td>
		</tr>
	</table>
</div>

<footer id="footer">
	EZ-MAN &bullet; 2013-<?php print date('Y');?> &bullet; Designed by 
	<span id="lnk_contact">Kevin Xian
		<div id="contact_info_panel">
			<i class="arrow"></i>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="header" colspan="2">Contact me</td>
				</tr>
				<tr>
					<td class="title email"></td>
					<td><a href="mailto:37207030@qq.com">37207030@qq.com</a></td>
				</tr>
				<tr>
					<td class="title wechat"></td>
					<td><img src="images/qrcode_kevin.jpg"></td>
				</tr>
			</table>
		</div>
	</span>
</footer>

<div id="alert_panel" class="overlay">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="middle" align="center" class="alert_holder"></td>
		</tr>
	</table>
</div>

</body>
</html>

<script src="http://rescdn.qqmail.com/node/ww/wwopenmng/js/sso/wwLogin-1.0.0.js"></script>
<script>
var response = '<?php print $_COOKIE['response'];?>',
	default_lang = '<?php print $_SESSION['u_lang'];?>';
$(function() {
	<?php if($_COOKIE['mode']==""){ ?>
	window.WwLogin({
		'id' : 'qrcode_holder',  
		'appid' : '<?php print $GLOBALS['zhiku_appid'];?>',
		'agentid' : '<?php print $GLOBALS['zhiku_agentid'];?>',
		'redirect_uri' :'<?php print $GLOBALS['redirect_uri'];?>',
		'state' : '',
		'href' : '',
	});
	<?php } ?>
	<?php if($_COOKIE['mode']=="preview"){ ?>
	$('#btn_login').click(function(){
		manage_process('login');
	});
	
	$('#btn_reset').click(function(){
		manage_process('reset');
	});
	
	$('#btn_forgot').click(function(){
		$('.reset_holder').addClass('show');
	});
	$('#btn_back').click(function(){
		$('.reset_holder').removeClass('show');
	});
	<?php } ?>
	
	if(response!=''){
		var response_str = '';
		switch(response){
			case 'logout': 
				response_str = '成功登出，再见！'; 
				show_alert(response_str);
				break;
			case 'timeout': 
				response_str = '您的登录状态已过期，请重新登录！'; 
				show_alert(response_str);
				break;
			case 'unknown': 
				response_str = '发生未知错误，请重新登录！'; 
				show_alert(response_str);
				break;
			default:
				show_alert(response);
		}
	}
});

function show_alert(msg,type,link){
	if(msg!=''){
		var msg_str = '',
			btn_str = '';	
		switch(type){
			case 'load': 
				msg_str = '<div class="loading"><i></i>'+msg+'</div>'; 
				break;
			case 'reload':
				msg_str = '<p>'+msg+'</p>';
				btn_str = '<p><button class="ctrl_btn active" onclick="window.location.reload();">确定</button></p>';
				break;
			case 'redirect':
				msg_str = '<p>'+msg+'</p>';
				btn_str = '<p><button class="ctrl_btn active" onclick="document.cookie=\'response=;path=./\';window.location.href=\''+link+'\'">确定</button></p>';
				break;
			default: 
				msg_str = '<p>'+msg+'</p>'; 
				btn_str = '<p><button class="ctrl_btn active" onclick="document.cookie=\'response=;path=./\';$(\'.overlay\').hide().setOverlay();">确定</button></p>';
		}
		
		$('#alert_panel .alert_holder').html(msg_str+btn_str);
		if(!$('#alert_panel').is(':visible'))$('#alert_panel').show().setOverlay();
	}
}

function manage_process(e){
	if(check_fields(e)){
		if(e=='login'){		
			$('#alert_panel .alert_holder').html('<div class="loading"><i></i>正在登录，请稍候 ...</div>');
			$('#alert_panel').show().setOverlay();
			
			var params = 'action=login&account='+encodeURIComponent($('#login_account').val())+'&password='+encodeURIComponent($('#login_password').val());
			$.ajax({
				type: 'post',
				url: 'manage_login.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						window.location.href = result.referer;
					}
					else{
						show_alert(result.error);
					}
				}
			});
		}
		else{
			$('#alert_panel .alert_holder').html('<div class="loading"><i></i>正在发送邮件，请稍候 ...</div>');
			$('#alert_panel').show().setOverlay();
			
			var params = 'action=reset_password&email='+encodeURIComponent($('#reset_email').val());
			$.ajax({
				type: 'post',
				url: 'manage_login.php',
				dataType: 'json',
				data: params,
				success: function(result){
					if(result.success==1){
						show_alert('新密码已经发送到您的邮箱，请使用新的密码登录。');
					}
					else{
						show_alert(result.error);
						$('#reset_email').val('');
					}
				}
			});
		}
	}
}

function check_fields(e){
	var valid_flag = true;
	$('.'+e+'_field').each(function(){
		if($(this).val()==''){
			show_alert('请填写电子邮箱及密码！');
			valid_flag = false;
		}
	});
	return valid_flag;
}
</script>