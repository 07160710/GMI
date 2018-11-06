<?php
if($action=="add"){
	$g_id = mysql_escape_string($_REQUEST['g_id']);
	$sql = "SELECT name FROM user_group WHERE id='$g_id'";
	$p_name = mysql_result(mysql_query($sql),0);
	
	$title_str = "在[$p_name]下创建群组";
	$parent_id = mysql_escape_string($_REQUEST['g_id']);
}
if($action=="edit"){
	$g_id = $_REQUEST['id'];
	
	$sql = "SELECT *  
			FROM user_group 
			WHERE id='$g_id'";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		$row = mysql_fetch_array($stmt);
		$parent_id = $row['parent_id'];
		$g_name = $row['name'];
		
		$auth_ctrl = $row['auth_ctrl'];
		$auth_ctrl_arr = json_decode($auth_ctrl, true);
		foreach($GLOBALS['user_auth'] as $auth){
			${$auth} = $auth_ctrl_arr[$auth];
		}
		
		$branch = $row['branch'];
		$branch_arr = json_decode($branch, true);
		
		$role = $row['role'];
		$role_arr = explode(",",$role);
	}	
	$title_str = "编辑用户群组[$g_name]";
}
setcookie("account_id","g_$g_id");
?>
<div id="user_info">
	<form id="account_form" enctype="multipart/form-data" autocomplete="off" onsubmit="return false;">	
		<table class="data_holder" border="0" cellpadding="0" cellspacing="10">
			<tr>
				<td class="header" colspan="2"><?php print $title_str;?></td>			
			</tr>
			<tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="10">
						<tr valign="top">
							<td class="title" height="32">
								部门名称
							</td>
							<td>
								<input name="group_name" type="text" id="group_name" value="<?php print $g_name;?>">
							</td>
						</tr>
						<?php if($g_id!=""){ ?>
						<tr>
							<td class="title" height="32">
								成员权限
							</td>
							<td valign="top">
								<div class="alert">
									注意：这是部门内全局设定，设置后将会改写部门内成员的权限，请谨慎操作。
								</div>
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
							<td class="title" height="32">
								成员角色
							</td>
							<td valign="top">
								<div class="alert">
									注意：这是部门内全局设定，设置后将会改写部门内成员的角色，请谨慎操作。
								</div>
								
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
						<?php } ?>
						<tr valign="top">
							<td></td>
							<td>
								<button name="save" class="ctrl_btn active save" style="margin:0;" onclick="save_page_info();">
									保存
								</button>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
function save_page_info(){
	show_alert('正在保存用户群组，请稍候 ...','load');
	var type = '<?php print $action;?>';
	var params = $('#account_form').serialize();
	params += '&action=save_group&type='+type+'&parent_id=<?php print $parent_id;?>&g_id=<?php print $g_id;?>';	
	$.ajax({
		type: 'post',
		url: 'account_manage.php',
		dataType: 'json',
		data: params,
		success: function(result){
			if(result.success==1){
				show_alert('成功保存用户群组！');
				if(type=='add')window.location.reload();
			}
			else{
				alert(result.error);
			}	
		}
	});
}
</script>