<?php
include("header.php");
?>
<table id="content_holder" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td id="left_col">
			<div id="left_col_holder">
				<ul class="node_tree">
					<?php if($_SESSION['level']>=2){ ?>
					<li id="g_0">						
						<div class="node_holder">
							<span class="node">
								<a class="node_link acc_root">
									用户群组
								</a>
								<?php
								if($_SESSION['level']==3){
									print "	<div class=\"menu_holder\">
												<div class=\"ctrl_menu\">
													<a class=\"item import\" onclick=\"import_member();\">
														导入成员
													</a>
												</div>
											</div>";
								}
								?>
							</span>
						</div>
					<?php } ?>
<?php
function get_user($department, $display=''){
	$sql = "SELECT id,name,status 
			FROM user 
			WHERE department='$department' AND level<='".$_SESSION['level']."'
			ORDER BY status DESC,CONVERT(name USING GBK)";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			$id = $row['id'];
			$name = $row['name'];
			
			$ctrl_menu_item = "";
			if($_SESSION['level']>1){
				$ctrl_menu_item .= "<a class=\"item move\" onclick=\"move_node('u_$id');\">移动</a>";
				$ctrl_menu_item .= "<a class=\"item delete\" onclick=\"delete_account('$id');\">删除</a>";
			}
			
			$ctrl_menu = "";
			if($ctrl_menu_item!=""){
				$ctrl_menu .= 	"<div class=\"ctrl_menu\">".
									$ctrl_menu_item.
								"</div>";
			}
			
			$status = $row['status'];
			$hide_class = ($status!=1)?"hide":"";
			
$user_print .= <<<USER
<li id="u_$id" class="user">
	<div class="node_holder $hide_class">
		<span class="node">
			<a class="node_link user" onclick="edit_node('u_$id');" title="$name">$name</a>
			<div class="menu_holder">
				$ctrl_menu
			</div>
		</span>
	</div>
</li>
USER;
		}
		
$user_print = <<<USER
<ul class="branch user_list $display">
	$user_print
</ul>
USER;
	}
	return $user_print;
}

function build_group_cond(){
	$cond_str = "";
	$sql = "SELECT route FROM user_group WHERE id='".$_SESSION['department']."'";
	$route = mysql_result(mysql_query($sql),0);
	$route_arr = explode("/",$route);
	foreach($route_arr as $g_id){
		if($cond_str!="")$cond_str .= ",";
		$cond_str .= "'".$g_id."'";
	}
	return $cond_str;
}

function get_group($parent_id){
	$cond_str = (($_SESSION['level']<3))?" AND id IN (".build_group_cond().")":"";
	
	$sql = "SELECT id,name 
			FROM user_group 
			WHERE parent_id='$parent_id' $cond_str 
			ORDER BY sort_order DESC";
	$stmt = mysql_query($sql);
	if(mysql_num_rows($stmt)>0){
		while($row = mysql_fetch_array($stmt)){
			$id = $row['id'];
			$name = $row['name'];
			
			$toogle_str = "";
			$sub_group_print = "";
			$menu_item = "";
			$user_print = "";
			if(has_child($id, "user_group")){
				$toogle_str = "<a class=\"toogle expand\" onclick=\"toogle_group('g_$id');\"></a>";
				$sub_group_print .= get_group($id);
				
				if($_SESSION['level']>2){
					$menu_item .= "<a class=\"item sort\" onclick=\"sort_node('$id')\">群组排序</a>";
				}
				
				$sql = "SELECT COUNT(*) FROM user WHERE department='$id'";
				$has_user = mysql_result(mysql_query($sql),0);
				if($has_user>0){
					$sub_group_print .= get_user($id, 'show');
				}
			}
			else{
				$sql = "SELECT COUNT(*) FROM user WHERE department='$id'";
				$has_user = mysql_result(mysql_query($sql),0);
				if($has_user>0){
					$toogle_str = "<a class=\"toogle shrink\" onclick=\"toogle_group('g_$id');\"></a>";
					$user_print .= get_user($id);
				}
			}
			
			if($_SESSION['level']>1 && $id==$_SESSION['department'] || $_SESSION['level']>2){
				$menu_item .= "<a class=\"item add\" onclick=\"add_group('$id')\">添加群组</a>";
				$menu_item .= "<a class=\"item add-user\" onclick=\"add_user('$id')\">添加成员</a>";
				if($_SESSION['level']>2)$menu_item .= "<a class=\"item delete\" onclick=\"delete_group('$id')\">删除群组</a>";
			}
			
			$ctrl_menu = "";
			if($menu_item!=""){
				$ctrl_menu = "	<div class=\"menu_holder\">
									<div class=\"ctrl_menu\">
										$menu_item
									</div>
								</div>";
			}
			
$group_print .= <<<GROUP
<li id="g_$id" class="group">
	$toogle_str
	<div class="node_holder">
		<span class="node">
			<a class="node_link group" onclick="edit_node('g_$id');" title="$name">$name</a>
			$ctrl_menu
		</span>
	</div>
	$sub_group_print
	$user_print
</li>
GROUP;
		}
		
$group_print = <<<GROUP
<ul class="branch group_list">
	$group_print
</ul>
GROUP;
	}
	return $group_print;
}

$sql = "SELECT id,name 
		FROM user_group 
		WHERE parent_id='0'";
$stmt = mysql_query($sql);
if(mysql_num_rows($stmt)>0){
	$group_str = "";
	while($row = mysql_fetch_array($stmt)){
		$g_id = $row['id'];
		$g_name = $row['name'];
		
		$toogle_str = "";
		$menu_item = "";
		$child_print = "";
		if(has_child($g_id, "user_group")){
			$toogle_str = "<a class=\"toogle expand\" onclick=\"toogle_group('g_$g_id');\"></a>";
			$child_print .= get_group($g_id);
			
			if($_SESSION['level']>2){
				$menu_item .= "<a class=\"item sort\" onclick=\"sort_node('$g_id')\">群组排序</a>";
			}
		}
		$child_print .= get_user($g_id, 'show');
		
		$menu_item .= "<a class=\"item add\" onclick=\"add_group('$g_id')\">添加群组</a>";
		$menu_item .= "<a class=\"item add-user\" onclick=\"add_user('$g_id')\">添加成员</a>";
		
		$ctrl_menu = "";
		if($menu_item!="" && ($_SESSION['level']>1 && $id==$_SESSION['department'] || $_SESSION['level']>2)){
			$ctrl_menu = "	<div class=\"menu_holder\">
								<div class=\"ctrl_menu\">
									$menu_item
								</div>
							</div>";
		}
		
if($_SESSION['level']>2){//超管
$group_str .= <<<group
<li id="g_$g_id" class="group">
	$toogle_str
	<div class="node_holder">
		<span class="node">
			<a class="node_link group" onclick="edit_node('g_$g_id');" title="$g_name">$g_name</a>
			$ctrl_menu
		</span>
	</div>
	$child_print
</li>
group;
}
else{
$group_str .= <<<group
<li id="g_$g_id" class="group">
	$toogle_str
	<div class="node_holder">
		<span class="node">
			<a class="node_link group" title="$g_name">$g_name</a>
			$ctrl_menu
		</span>
	</div>
	$child_print
</li>
group;
}
	}
$group_print = <<<GROUP
<ul class="branch group_list">
	$group_str
</ul>
GROUP;
}

print $group_print;
?>
				</ul>
			</div>
		</td>
		<td id="right_col">
			<div id="content_panel"></div>
		</td>
	</tr>
</table>

<script>
var post_id = '<?php print $_COOKIE['account_id'];?>',
	post_url = 'account_manage.php',
	id_prefix = 'g_';

$(function() {
	if(post_id=='')post_id = 'g_1';
	edit_node(post_id);
});

function toogle_group(group_id){
	if($('#left_col_holder #'+group_id+' > .toogle').hasClass('expand')){//shrink
		$('#left_col_holder #'+group_id+' > .toogle').removeClass('expand').addClass('shrink');
		$('#left_col_holder #'+group_id+' > .branch').css('display','none');
	}
	else{//expand
		$('#left_col_holder #'+group_id+' > .toogle').removeClass('shrink').addClass('expand');
		$('#left_col_holder #'+group_id+' > .branch').css('display','block');
	}
}

function import_member(){
	if(confirm('确定要从微信企业号导入部门和成员信息？')){
		show_alert('正在从企业号导入部门组和成员，请稍候 ...','load');
		
		var params = '&action=import_member';	
		$.ajax({
			type: 'post',
			url: 'wechat_manage.php',
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					show_alert(
						'成功导入部门和成员！<br/>' + 
						'部门读取->'+result.dept_import_num+'<br/>' + 
						'部门插入->'+result.dept_insert_num+'<br/>' + 
						'部门更新->'+result.dept_update_num+'<br/>' + 
						'部门删除->'+result.dept_delete_num+'<br/>' + 
						'成员读取->'+result.user_import_num+'<br/>' + 
						'成员插入->'+result.user_insert_num+'<br/>' + 
						'成员更新->'+result.user_update_num+'<br/>' + 
						'成员删除->'+result.user_delete_num, 'reload'
					);
				}
				else{
					alert(result.error);
				}	
			}
		});
	}
}

function delete_group(id){
	if(confirm('将会从企业号同步删除此群组及成员，确定删除？')){
		var params = 'action=delete_group&group_id='+id;
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					document.cookie = 'account_id=g_'+result.g_id;
					show_alert('成功删除群组及成员','reload');
				}
				else{
					alert(result.error);
				}
			}
		});	
	}
}

function delete_account(id){
	if(confirm('将会从企业号同步删除此账户，确定删除？')){
		var params = 'action=delete_user&acc_id='+id;
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					document.cookie = 'account_id=g_'+result.g_id;
					show_alert('成功删除账户','reload');
				}
				else{
					alert(result.error);
				}
			}
		});	
	}
}

function add_group(id){
	id_prefix = 'g_';
	set_curr(id);
	$('#content_panel').html('<div class="loading cover"></div>').load('account_control.php?action=add&object=group&g_id='+id);
}
function add_user(id){
	id_prefix = 'g_';
	set_curr(id);
	$('#content_panel').html('<div class="loading cover"></div>').load('account_control.php?action=add&object=user&g_id='+id);
}

function edit_node(acc_id){
	var id_arr = acc_id.split('_'),
		type = id_arr[0],
		id = id_arr[1];
	
	id_prefix = type+'_';
	set_curr(id);
	
	var group_id = acc_id;
	if(type=='u')group_id = $('#'+acc_id).parents('.group').attr('id');
	
	$('#left_col_holder #'+group_id+' > .toogle').removeClass('shrink').addClass('expand');
	$('#left_col_holder #'+group_id+' > .user_list').show();
	
	$('#content_panel').html('<div class="loading cover"></div>').load('account_control.php?action=edit&object='+((type=='g')?'group':'user')+'&id='+id);
}
</script>

<?php
include("public_content.php");
include("footer.php");
?>