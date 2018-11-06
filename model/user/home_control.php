<?php
require_once("check_login.php");
require_once("include/conn.php");
require_once("function.php");

if(isset($_REQUEST['ctrl']))$_SESSION['home_ctrl'] = $_REQUEST['ctrl'];

$select_field_str = "";
foreach($GLOBALS['user_fields'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= "user.".$key;
}
foreach($GLOBALS['user_fields_wx'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= "user.".$key;
}
foreach($GLOBALS['user_fields_ext'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= "user.".$key;
}

$sql = "SELECT 
			$select_field_str 
		FROM user 
		WHERE id='".$_SESSION['u_id']."'";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	$row = mysql_fetch_array($stmt);
	$username = $row['name'];
	$mobile = $row['mobile'];
	$email = $row['email'];
	$level = $row['level'];
	$position = $row['position'];
	$acc_image = $row['avatar'];
	$last_login = $row['last_login'];
	$last_logout = $row['last_logout'];

	$user_level_str = "";
	foreach($GLOBALS['user_level'] as $key=>$val){
		if($level==$key)$user_level_str = $val;
	}
	
	if($_SESSION['home_ctrl']=="basic_info"){
	?>
	<div id="login_info">
		<table class="data_holder" border="0" cellpadding="0" cellspacing="10">
			<tr>
				<td class="title">
					最后登陆：
				</td>
				<td><?php print ($last_login!=0)?format_time($last_login):"N/A";?></td>
			</tr>
			<tr>
				<td class="title">					
					最后登出：
				</td>
				<td><?php print ($last_logout!=0)?format_time($last_logout):"N/A";?></td>
			</tr>
		</table>
	</div>
	<?php
	}	
	if($_SESSION['home_ctrl']=="edit_profile"){
?>
	<div id="change_info_holder">
		<table class="data_holder" border="0" cellpadding="0" cellspacing="10">
			<tr>
				<th align="left" colspan="2">
					用户个人信息
				</th>
			</tr>
			<tr>
				<td class="title">
					用户名：
				</td>
				<td>
					<input name="username" type="text" value="<?php print $username;?>">
				</td>
			</tr>
			<tr>
				<td class="title">
					手机号码：
				</td>
				<td>
					<label><?php print $mobile;?></label>
				</td>
			</tr>
			<tr>
				<td class="title">
					电子邮箱：
				</td>
				<td>
					<input name="email" type="text" value="<?php print $email;?>">
				</td>
			</tr>
			<tr>
				<td class="title">
					职位：
				</td>
				<td>
					<input name="position" type="text" value="<?php print $position;?>">
				</td>
			</tr>
			<tr>
				<td class="title align-top">
					用户头像：
				</td>
				<td>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr valign="top">
							<td id="img_ctrl_area">
								<div id="img_ctrl_holder" class="display_area">
									<img src="<?php print $acc_image;?>">
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="title">
					用户类别：
				</td>
				<td><?php print $user_level_str;?></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<button class="ctrl_btn active save" onclick="save_page_info('edit_profile');" style="margin:0;">
						保存
					</button>
				</td>
			</tr>
		</table>
</div>
	<?php
	}				
	if($_SESSION['home_ctrl']=="reset_pass"){
	?>
	<div id="reset_pass_holder">
		<table class="data_holder" border="0" cellpadding="0" cellspacing="10">
			<tr>
				<td></td>
				<td class="alert_holder">
					请设置6-20位字母和数字组合的密码。
				</td>
			</tr>
			<tr>
				<td class="title">
					旧密码：
				</td>
				<td>
					<input name="old_password" type="password" id="old_password">
				</td>
			</tr>
			<tr>
				<td class="title">
					新密码：
				</td>
				<td>
					<input name="new_password" type="password" id="new_password">
				</td>
			</tr>
			<tr>
				<td class="title">
					确认密码：
				</td>
				<td>
					<input name="cfm_password" type="password" id="cfm_password">
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<button class="ctrl_btn active reset" onclick="save_page_info('reset_pass');" style="margin:0;">
						修改密码
					</button>
				</td>
			</tr>
		</table>
	</div>
	<?php
	}
	if($_SESSION['home_ctrl']=="site_setting"){
		$sql = "SELECT * FROM site_table WHERE id='".$_SESSION['site_id']."'";
		$get_setting = mysql_query($sql);
		if(mysql_num_rows($get_setting)>0){
			$row = mysql_fetch_array($get_setting);
			$mailbox_type = $row['mailbox_type'];
			$smtpsender = $row['smtpsender'];
			$smtpserver = $row['smtpserver'];
			$smtpserverport = $row['smtpserverport'];
			$smtpmail = $row['smtpmail'];
			$smtpuser = $row['smtpuser'];
			
			$qywx_appid = $row['qywx_appid'];
			$qywx_agentid = $row['qywx_agentid'];
			$qywx_appsecret = $row['qywx_appsecret'];
		}
	?>
	<div id="site_setting_holder">
		<table class="data_holder" border="0" cellpadding="0" cellspacing="10">
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="0" cellspacing="10" style="border:1px dashed #ccc;border-radius:5px;">
						<tr>
							<th colspan="2">邮箱设置</th>
						</tr>
						<tr>
							<td class="title">
								邮箱类型：
							</td>
							<td>					
								<input type="radio" name="mailbox_type" id="internal" value="0" <?php if($mailbox_type!=1)print "checked";?>>
								<label for="internal">
									使用服务器内部邮箱
								</label>
								<div class="clear"></div>
								<input type="radio" name="mailbox_type" id="external" value="1" <?php if($mailbox_type==1)print "checked";?>>
								<label for="external">						
									使用外部第三方邮箱
								</label>
							</td>
						</tr>
						<tr>
							<td class="title">
								发件人姓名：
							</td>
							<td>
								<input type="text" name="smtpsender" value="<?php print $smtpsender;?>">
							</td>
						</tr>
						<tr class="external_row">
							<td class="title">
								SMTP服务器：
							</td>
							<td>
								<input type="text" name="smtpserver" value="<?php print $smtpserver;?>">
							</td>
						</tr>
						<tr class="external_row">
							<td class="title">
								SMTP服务器端口：
							</td>
							<td>
								<input type="text" name="smtpserverport" class="short" value="<?php print ($smtpserverport!=0)?$smtpserverport:"25";?>">
							</td>
						</tr>
						<tr class="external_row">
							<td class="title">
								邮箱地址：
							</td>
							<td>
								<input type="text" name="smtpmail" value="<?php print $smtpmail;?>">
							</td>
						</tr>
						<tr class="external_row">
							<td class="title">
								用户名：
							</td>
							<td>
								<input type="text" name="smtpuser" value="<?php print $smtpuser;?>">
							</td>
						</tr>
						<tr class="external_row">
							<td class="title">
								密码：
							</td>
							<td>
								<input type="text" name="smtppass">
								<div class="note">由于安全原因，密码被隐藏。如需更改，请修改后保存。</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="0" cellspacing="10" style="border:1px dashed #ccc;border-radius:5px;">
						<tr>
							<th colspan="2">企业号设置</th>
						</tr>
						<tr>
							<td class="title">
								AppID：
							</td>
							<td>					
								<input type="text" name="qywx_appid" value="<?php print $qywx_appid;?>">
							</td>
						</tr>
						<tr>
							<td class="title">
								AgentID：
							</td>
							<td>
								<input type="text" name="qywx_agentid" value="<?php print $qywx_agentid;?>">
							</td>
						</tr>
						<tr>
							<td class="title">
								AppSecret：
							</td>
							<td>
								<input type="text" name="qywx_appsecret" class="ex-long">
								<div class="note">由于安全原因，AppSecret被隐藏。如需更改，请修改后保存。</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<button class="ctrl_btn active reset" id="btn_change" onclick="save_page_info('site_setting');">
						保存
					</button>
				</td>
			</tr>
		</table>
		<script>
			if($('input[name=mailbox_type]:checked').val()!=1){
				$('.external_row').hide();
			}
			$('input[name=mailbox_type]').change(function(){
				if($(this).attr('id')=='internal')$('.external_row').hide();
				else $('.external_row').show();
			});
			$('input[name=smtppass]').bind('focus', function(){
				$(this).attr('type', 'password');
			});
		</script>
	</div>
	<?php	
	}
}
?>

<script>
$(function() {
	set_img_upload();
	set_public_attr();
	
	$('.ctrl_arrow').unbind('click').click(function(){
		$(this).parent('.menu_holder').children('.ctrl_menu').show();
	});
	
	$('body').click(function(event){//for cell phone
		var $target = $(event.target);
		if($target.is('.ctrl_arrow')){
		}
		else{
			$('.ctrl_menu').hide();
		}
	});
	
	$('.ctrl_menu .btn_file').mouseenter(function(){
		$(this).parent('.ctrl_menu').children('.item:eq(0)').css('background','#d5effc');
	}).mouseleave(function(){
		$(this).parent('.ctrl_menu').children('.item:eq(0)').css('background','transparent');
	});
});

function delete_img(){
	if(confirm('确定要删除此头像？')){
		show_alert('正在删除头像，请稍候 ...','load');
		
		$.ajax({
			type: 'post',
			url: 'img_delete.php?acc_id=<?php print $_SESSION['u_id'];?>',
			dataType: 'json',
			success: function(result){
				if(result.success==1){
					$('#img_ctrl_area').html(
						'<div id="img_ctrl_holder" class="upload_area">' + 
							'<input name="acc_image" class="btn_file" type="file" id="acc_image">' + 
							'<span class="btn_upload">上传图片</span>' + 
						'</div>'
					);
					$('#user_img').attr('src','<?php print _ROOT_URL_;?>images/no_profile_image.jpg');
					set_public_attr();
					set_img_upload();
					show_alert('成功删除头像！');
				}
				else{
					show_alert(result.error);
				}
			}
		});
	}
}

function upload_img(){	
	$.ajaxFileUpload({
		url:'img_upload.php?acc_id=<?php print $_SESSION['u_id'];?>&object=account',
		secureuri :false,
		fileElementId :'acc_image',
		dataType : 'json',
		success : function (result){
			if(result.success==1){
				var delete_ctrl_str = 	'<a class="ctrl_arrow"></a>' + 
										'<div class="ctrl_menu">' + 
											'<input name="acc_image" class="btn_file" type="file" id="acc_image">' + 
											'<a class="item replace">替换</a>' + 
											'<a class="item crop" onclick="crop_img(\'<?php print _ROOT_URL_;?>'+result.file_url+'\');">裁切</a>' + 
											'<a class="item delete" onclick="delete_img(\'<?php print $_SESSION['u_id'];?>\');">删除</a>' + 
										'</div>';
				
				$('#img_ctrl_area').html(
					'<div id="img_ctrl_holder" class="display_area">' + 
						'<img src="<?php print _ROOT_URL_;?>'+result.file_url+'">' + 
						'<div id="img_ctrl" class="menu_holder">' + 
							delete_ctrl_str + 
						'</div>' + 
					'</div>'
				);
				$('#user_img').attr('src','<?php print _ROOT_URL_;?>'+result.file_url);
				set_public_attr();
				set_img_upload();
				show_alert('成功上传头像！');
			}
			else{
				show_alert(result.error);
			}
		}
	});
}

function set_img_upload(){
	$('#acc_image').change(function(){
		show_alert('正在上传头像，请稍候 ...','load');
		upload_img();
	});
}

function save_page_info(e){
	show_alert('正在保存信息，请稍候 ...','load');
	
	var params = 'ctrl='+e;
	$('.data_holder input[type=hidden],.data_holder input[type=text],.data_holder input[type=password],.data_holder input[type=radio]:checked').each(function(){
		params += '&'+$(this).attr('name')+'='+encodeURIComponent($(this).val());
	});
	
	$.ajax({
		type: 'post',
		url: 'home_manage.php',
		dataType: 'json',
		data: params,
		success: function(result){
			if(result.success==1){
				if(result.action=='edit_profile'){
					show_alert('成功保存信息！');
				}
				else if(result.action=='reset_pass'){
					show_alert('成功修改密码！');
				}
				else if(result.action=='site_setting'){
					show_alert('成功保存站点设定！');
				}
			}
			else{
				show_alert(result.error);
			}	
		}
	});
}
</script>