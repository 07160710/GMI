<?php
if($_REQUEST['id']!=""){
	$acc_id = mysql_escape_string($_REQUEST['id']);	
	setcookie("account_id","u_$acc_id");
}

$select_field_str = "";
foreach($GLOBALS['user_fields'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= "user.".$key;
}
foreach($GLOBALS['user_fields_wx'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= "user.".$key;
	
	if($key=="department"){
		if($select_field_str!="")$select_field_str .= ",";
		$select_field_str .= "ug.name AS dept";
	}
}
foreach($GLOBALS['user_fields_ext'] as $key){
	if($select_field_str!="")$select_field_str .= ",";
	$select_field_str .= "user.".$key;
}

$sql = "SELECT 
			$select_field_str,
			user.branch,
			user.role 
		FROM user 
			LEFT JOIN user_group ug ON user.department=ug.id 
		WHERE user.id='$acc_id'";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	$row = mysql_fetch_array($stmt);
	
	$userid = $row['userid'];
	$acc_name = $row['name'];
	$name_en = $row['name_en'];
	$mobile = $row['mobile'];
	$email = $row['email'];
	$g_id = $row['department'];
	$is_leader = $row['is_leader'];
	$position = $row['position'];
	$gender = $row['gender'];
	$acc_level = $row['level'];
	$acc_avatar = $row['avatar'];
	$intro = $row['intro'];
	$acc_image = $row['image'];
	$acc_active = $row['status'];
	$last_login = ($row['last_login']!=0)?format_time($row['last_login']):"N/A";
	$last_logout = ($row['last_logout']!=0)?format_time($row['last_logout']):"N/A";
	
	$auth_ctrl = $row['auth_ctrl'];
	$auth_ctrl_arr = json_decode($auth_ctrl, true);
	foreach($GLOBALS['user_auth'] as $auth){
		${$auth} = $auth_ctrl_arr[$auth];
	}
	
	$branch = $row['branch'];
	$branch_arr = json_decode($branch, true);
	
	$role = $row['role'];
	$role_arr = explode(",",$role);
	
	$action = "update";
	$title_str = "编辑用户[$acc_name]";
}
else{	
	$g_id = mysql_escape_string($_REQUEST['g_id']);
	$sql = "SELECT name FROM user_group WHERE id='$g_id'";
	$p_name = mysql_result(mysql_query($sql),0);
	
	$action = "create";
	$title_str = "在[$p_name]下创建用户";
}
?>
<div id="user_info">
	<form id="account_form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">
		<input type="hidden" name="acc_id" id="acc_id" value="<?php print $acc_id;?>">
		<input type="hidden" name="g_id" id="g_id" value="<?php print $g_id;?>">
		<input type="hidden" name="action" id="action" value="<?php print $action;?>">
		<input type="text" style="position:absolute;top:-999px;"/>
		<input type="password" style="position:absolute;top:-999px;"/>
		<table class="data_holder" border="0" cellpadding="0" cellspacing="10">
			<tr>
				<td class="header" colspan="2"><?php print $title_str;?></td>			
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="0" cellspacing="10">
						<tr>
							<th align="left" colspan="2">
								用户信息
							</th>
						</tr>
						<tr>
							<td class="title">
								用户名：
							</td>
							<td>
								<input name="name" type="text" value="<?php print $acc_name;?>">
							</td>
						</tr>
						<tr>
							<td class="title">
								英文名：
							</td>
							<td>
								<input name="name_en" type="text" value="<?php print $name_en;?>">
							</td>
						</tr>
						<tr>
							<td class="title">
								手机号码：
							</td>
							<td>
								<input name="mobile" type="text" value="<?php print $mobile;?>">
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
						<tr valign="top">
							<td class="title">
								新密码：
							</td>
							<td>
								<input style="display:none">
								<input name="new_password" type="password">
								<div class="clear note">
									最少6位，最多20位的数字/英文字母组合。
								</div>
							</td>
						</tr>
						<tr>
							<td class="title">
								确认密码：
							</td>
							<td>
								<input name="cfm_password" type="password">
							</td>
						</tr>
						<tr>
							<td class="title">
								用户身份：
							</td>
							<td>
								<select name="level" id="level">
								<?php
								foreach($user_level as $key=>$val){
									$selected = "";
									if($acc_level==$key){
										$selected = "selected";
									}
									if($key<=$_SESSION['level']){
										print "<option value=\"$key\" $selected>$val</option>";
									}
								}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="title">
								所在部门：
							</td>
							<td>
								<select name="department" id="department">
								<?php
								function get_dept($parent_id, $level=0){
									global $g_id;
									$dept_print = "";
									
									$sql = "SELECT id,name FROM user_group WHERE parent_id='$parent_id' ORDER BY sort_order DESC";
									$get_dept = mysql_query($sql);
									if(mysql_num_rows($get_dept)>0){
										$level++;
										while($row = mysql_fetch_array($get_dept)){
											$dept_id = $row[0];
											$dept_name = $row[1];
											$selected = "";
											if($g_id==$dept_id)$selected = "selected";
											
											$spacer = "";
											for($i=1;$i<=$level;$i++){
												$spacer .= "&nbsp;&nbsp;&nbsp;&nbsp;";
											}
											
											$dept_print .= "<option value=\"$dept_id\" $selected>".$spacer.$dept_name."</option>";
											
											if(has_child($dept_id, "user_group")){
												$dept_print .= get_dept($dept_id, $level);
											}
										}
									}
									return $dept_print;
								}
								
								$sql = "SELECT id,name FROM user_group WHERE parent_id='0' ORDER BY sort_order DESC";
								$get_dept = mysql_query($sql);
								if(mysql_num_rows($get_dept)>0){
									while($row = mysql_fetch_array($get_dept)){
										$dept_id = $row[0];
										$dept_name = $row[1];
										$selected = "";
										if($g_id==$dept_id)$selected = "selected";
										
										print "<option value=\"$dept_id\" $selected>$dept_name</option>";
										
										if(has_child($dept_id, "user_group")){
											print get_dept($dept_id);
										}
									}
								}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="title">
								是否主管：
							</td>
							<td>
								<label for="is_leader">
									<input type="checkbox" name="is_leader" id="is_leader" <?php print ($is_leader==1)?"checked":"";?>>
									是
								</label>
							</td>
						</tr>
						<tr>
							<td class="title">
								性别：
							</td>
							<td>
								<select name="gender" id="gender">
								<?php
								foreach($GLOBALS['gender_opt'] as $key=>$val){
									$selected = ($gender==$key)?"selected":"";
									if($key<=$_SESSION['level']){
										print "<option value=\"$key\" $selected>$val</option>";
									}
								}
								?>
								</select>
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
						<?php if($acc_id!=""){ ?>
						<tr valign="top">
							<td class="title">
								用户头像：
							</td>
							<td id="avatar_holder">
								<?php
								if($acc_avatar!="")print "	<div class=\"display_area\"><img src=\"$acc_avatar\"></div>";
								?>
							</td>
						</tr>
						<tr valign="top">
							<td class="title">
								正装照：
							</td>
							<td>
<table border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td id="img_ctrl_area">
						
<?php
$print_img_ctrl = "";

if($acc_image!="" && file_exists(_ROOT_PATH_.$acc_image)){	
	$delelte_ctrl_str = "";
	$delete_id = $acc_id;
	$acc_image_src = _ROOT_URL_.$acc_image."?".time();
	$acc_full_image = _ROOT_URL_.str_replace(_THUMB_FOLDER_,"",$acc_image);
	
$delelte_ctrl_str = <<<DELETE_CTRL
<a class="ctrl_arrow"></a>
<div class="ctrl_menu">
	<input name="acc_image" class="btn_file" type="file" id="acc_image">
	<a class="item replace">替换</a>
	<a class="item crop" onclick="crop_img('$acc_full_image');">裁切</a>
	<a class="item delete" onclick="delete_img('$delete_id');">删除</a>
</div>
DELETE_CTRL;

$print_img_ctrl = <<<IMG
	<div id="img_ctrl_holder" class="display_area">
		<img src="$acc_image_src">
		<div id="img_ctrl" class="menu_holder">
			$delelte_ctrl_str
		</div>
		<span class="upload_info"></span>
	</div>	
IMG;
	
}
else{
$print_img_ctrl = <<<IMG
	<div id="img_ctrl_holder" class="upload_area">							
		<input name="acc_image" class="btn_file" type="file" id="acc_image">
		<span class="btn_upload">上传图片</span>
		<span class="upload_info"></span>
	</div>
IMG;
	
}

print $print_img_ctrl;
?>						
						
		</td>
	</tr>
</table>
							</td>
						</tr>
						<tr valign="top">
							<td class="title">
								个人简介：
							</td>
							<td>
								<textarea name="intro" style="width:90%;height:100px;"><?php print $intro;?></textarea>
							</td>
						</tr>
						<?php } ?>
						<tr valign="top">
							<td class="title">
								是否激活：
							</td>
							<td>
								<label for="active_yes">
									<input type="radio" id="active_yes" name="active" value="1" <?php print ($acc_active==1)?"checked":"";?>>
									是
								</label>
								<div class="clear"></div>					
								<label for="active_no">
									<input type="radio" id="active_no" name="active" value="0" <?php print ($acc_active!=1)?"checked":"";?>>
									否
								</label>
							</td>
						</tr>
						<tr valign="top">
							<td></td>
							<td>
								<button name="save" class="ctrl_btn active save" style="margin:0;" onclick="save_page_info('account');">
									保存
								</button>
							</td>
						</tr>
						<?php if($acc_id!=""){ ?>
						<tr>
							<td class="title">
								上次登录时间：
							</td>
							<td><?php print $last_login;?></td>
						</tr>
						<tr>
							<td class="title">
								上次登出时间：
							</td>
							<td><?php print $last_logout;?></td>
						</tr>
						<?php }	?>
					</table>
				</td>
				<?php if($_SESSION['level']>1 && $acc_id!=""){ ?>
				<td width="50%" valign="top">
					<table width="100%" border="0" cellpadding="0" cellspacing="10">
						<tr>
							<th align="left" colspan="2">
								用户权限
							</th>
						</tr>
						<tr>
							<td class="title" height="32">
								智库权限
							</td>
						</tr>
						<tr>
							<td valign="top">
								<!-- Content Auth -->
								<div class="auth_holder">
									<div class="title">内容权限</div>
									<select name="auth_content">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_content==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
								
								<!-- CBase Auth -->
								<div class="auth_holder">
									<div class="title">认定库权限</div>
									<select name="auth_cbase">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_cbase==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
								
								<!-- NBase Auth -->
								<div class="auth_holder">
									<div class="title">通知库权限</div>
									<select name="auth_nbase">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_nbase==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td class="title" height="32">
								协同权限
							</td>
						</tr>
						<tr>
							<td valign="top">
								<!-- PBase Auth -->
								<div class="auth_holder">
									<div class="title">项目库权限</div>
									<select name="auth_pbase">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_pbase==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
								
								<!-- Platform Auth -->
								<div class="auth_holder">
									<div class="title">平台权限</div>
									<select name="auth_platform">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_platform==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
								
								<!-- Company Auth -->
								<div class="auth_holder">
									<div class="title">公司权限</div>
									<select name="auth_company">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_company==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
								
								<!-- Agreement Auth -->
								<div class="auth_holder">
									<div class="title">协议权限</div>
									<select name="auth_agreement">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_agreement==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
								
								<!-- Project Auth -->
								<div class="auth_holder">
									<div class="title">项目权限</div>
									<select name="auth_project">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_project==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
								
								<!-- Sales Auth -->
								<div class="auth_holder">
									<div class="title">销售权限</div>
									<select name="auth_sales">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_sales==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
								
								<!-- Technology Auth -->
								<div class="auth_holder">
									<div class="title">技术权限</div>
									<select name="auth_technology">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_technology==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
								
								<!-- Finance Auth -->
								<div class="auth_holder">
									<div class="title">财务权限</div>
									<select name="auth_finance">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_finance==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
								
								<!-- Media Auth -->
								<div class="auth_holder">
									<div class="title">附件权限</div>
									<select name="auth_media">
									<?php
									foreach($GLOBALS['auth_ctrl_opt'] as $key=>$name){
										print "<option value=\"$key\" ".(($auth_media==$key)?"selected":"").">$name</option>";
									}
									?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td class="title" height="32">
								所属公司
							</td>
						</tr>
						<tr>
							<td valign="top">
								<div class="alert">
									注意：这是部门内全局设定，设置后将会改写部门内成员的所属公司，请谨慎操作。
								</div>
								
								<div class="auth_holder">
									<?php
									$sql = "SELECT id,name FROM branch ORDER BY id";
									$get_branch = mysql_query($sql);
									while($row = mysql_fetch_array($get_branch)){
										$branch_id = $row[0];
										$branch_name = $row[1];
										
										$checked = "";
										if(in_array($branch_id,$branch_arr))$checked = "checked";
										
										print "	<label for=\"branch_$branch_id\">
													<input type=\"checkbox\" name=\"branch[]\" id=\"branch_$branch_id\" value=\"$branch_id\" $checked>
													$branch_name
												</label>";
									}
									?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<th align="left" colspan="2">
								用户角色
							</th>
						</tr>
						<tr>
							<td valign="top">
								<div class="auth_holder">
									<?php
									foreach($GLOBALS['user_role'] as $key=>$name){
										$checked = "";
										if(in_array($key,$role_arr))$checked = "checked";
										
										print "	<label for=\"role_$key\">
													<input type=\"checkbox\" name=\"role[]\" id=\"role_$key\" value=\"$key\" $checked>
													$name
												</label>";
									}
									?>
									</select>
								</div>
							</td>
						</tr>
					</table>
				</td>
				<?php } ?>
			</tr>
		</table>
	</form>
</div>

<script>
var targetID = '<?php print $acc_id;?>',
	prev_acc_id = '<?php print $_COOKIE['account_id'];?>';
$(function() {
	<?php if($acc_avatar==""){ ?>
	var params = '&action=get_avatar&userid=<?php print $userid;?>';	
	$.ajax({
		type: 'post',
		url: 'account_manage.php',
		dataType: 'json',
		data: params,
		success: function(result){
			if(result.success==1){
				$('#avatar_holder').html(
					'<div class="display_area">' + 
						'<img src="'+result.avatar+'">' + 
					'</div>'
				);
			}
			else{
				alert(result.error);
			}	
		}
	});
	<?php } ?>
	
	set_img_upload();
});

function delete_img(){
	if(confirm('确定要删除此头像？')){
		show_alert('正在删除头像，请稍候 ...','load');
		
		$.ajax({
			type: 'post',
			url: 'img_delete.php?acc_id='+targetID,
			dataType: 'json',
			success: function(result){
				if(result.success==1){
					$('#img_ctrl_area').html(
						'<div id="img_ctrl_holder" class="upload_area">' + 
							'<input name="acc_image" class="btn_file" type="file" id="acc_image">' + 
							'<span class="btn_upload">上传图片</span>' + 
						'</div>'
					);
					
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
		url:'img_upload.php?acc_id=<?php print $acc_id;?>&object=account',
		secureuri :false,
		fileElementId :'acc_image',
		dataType : 'json',
		success : function (result){
			if(result.success==1){
				var delete_ctrl_str = 	'<a class="ctrl_arrow"></a>' + 
										'<div class="ctrl_menu">' + 
											'<input name="acc_image" class="btn_file" type="file" id="acc_image">' + 
											'<a class="item replace">替换</a>' + 
											'<a class="item crop" onclick="crop_img(\'<?php print _BASE_URL_;?>'+result.file_url.replace('<?php print _THUMB_FOLDER_;?>','')+'\');">裁切</a>' + 
											'<a class="item delete" onclick="delete_img(\'<?php print $_SESSION['u_id'];?>\');">删除</a>' + 
										'</div>';
				
				$('#img_ctrl_area').html(
					'<div id="img_ctrl_holder" class="display_area">' + 
						'<img src="<?php print _BASE_URL_;?>'+result.file_url+'">' + 
						'<div id="img_ctrl" class="menu_holder">' + 
							delete_ctrl_str + 
						'</div>' + 
					'</div>'
				);
				
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

function save_page_info(){
	show_alert('正在保存成员信息，请稍候 ...','load');

	var params = $('#account_form').serialize();
	params += '&action=save_account';	
	$.ajax({
		type: 'post',
		url: 'account_manage.php',
		dataType: 'json',
		data: params,
		success: function(result){
			if(result.success==1){
				show_alert('成功保存成员信息！','reload');
			}
			else{
				alert(result.error);
			}	
		}
	});
}
</script>