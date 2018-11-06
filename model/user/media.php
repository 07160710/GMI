<?php
include("header.php");
?>
<table id="content_holder" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td id="left_col">
			<div id="left_col_holder">
				<form action="media" method="post" name="control_form" id="control_form" enctype="multipart/form-data">
					<input type="hidden" name="post_id" value="<?php print $_COOKIE['media_id'];?>">
					<input type="hidden" name="media_dir" value="<?php print _MEDIA_FOLDER_;?>">
				</form>
				<ul class="node_tree">
					<li id="m_0">						
						<div class="node_holder">
							<span class="node">
								<a class="node_link media_root">
									附件根目录
								</a>
								<div class="menu_holder">
<?php
$home_menu_item = "";
if($_SESSION['auth_media']==2){
	$home_menu_item .= "<a class=\"item add\" onclick=\"add_node('0');\">添加</a>";	
	if(has_child(0,'media')){
		$home_menu_item .= "<a class=\"item sort\" onclick=\"sort_node('0','folder')\">排序</a>";
	}
}

if($home_menu_item!=""){
	print  "<a class=\"ctrl_arrow\"></a>
			<div class=\"ctrl_menu\">
				$home_menu_item
			</div>";
}
?>
								</div>
							</span>
						</div>
<?php
$level = 1;
$print_media = "";
$sql = "SELECT id,name,type 
		FROM media_table 
		WHERE parent_id='0' AND 
			level='$level' AND 
			type='folder' 
		ORDER BY sort_order";
$get_media = mysql_query($sql);
if(mysql_num_rows($get_media)>0){
	$print_media .= "<ul class=\"branch l_$level\">";	
	while($m_row = mysql_fetch_array($get_media)){
		$m_id = $m_row['id'];
		$m_name = $m_row['name'];
		$m_type = $m_row['type'];
		
		$toogle_str = "";
		$sort_str = "";
		$child_str = "";
		if(has_child($m_id,'media_table')){
			$toogle_str = "<a class=\"toogle shrink\" onclick=\"toogle_node('$m_id');\"></a>";
			$sort_str = "<a class=\"item sort\" onclick=\"sort_node('$m_id','folder');\">排序</a>";
			$child_str = "<ul class=\"branch l_".($level+1)."\"></ul>";
		}
		
		$ctrl_menu_item = "";
		if($_SESSION['auth_media']==2){
			if($m_type=="folder"){
				$ctrl_menu_item .= "<a class=\"item add\" onclick=\"add_node('$m_id');\">添加</a>";
			}
			
			$ctrl_menu_item .= "<a class=\"item upload\" onclick=\"display_upload('$m_id');\">上传</a>";
			$ctrl_menu_item .= "<a class=\"item move\" onclick=\"move_node('$m_id');\">移动</a>";
			$ctrl_menu_item .= $sort_str;
			$ctrl_menu_item .= "<a class=\"item auth\" onclick=\"auth_manage('folder','$m_id');\">权限</a>";
			$ctrl_menu_item .= "<a class=\"item delete\" onclick=\"delete_node($m_id);\">删除</a>";
		}
		
		$ctrl_menu = "";
		if($ctrl_menu_item!=""){
			$ctrl_menu = "	<a class=\"ctrl_arrow\"></a>
							<div class=\"ctrl_menu\">
								$ctrl_menu_item
							</div>";
		}
		
$print_media .= <<<MEDIA
<li id="m_$m_id">
	$toogle_str
	<div class="node_holder">
		<span class="node">
			<a class="node_link $m_type" title="$m_name" onclick="edit_node('$m_id');">
				$m_name
			</a>
			<div class="menu_holder">
				$ctrl_menu
			</div>
		</span>
	</div>
	$child_str
</li>
MEDIA;
	
	}
	
	$print_media .= "</ul>";
}

print $print_media;
?>
					</li>
				</ul>
			</div>
		</td>
		<td id="right_col">
			<div id="content_panel"></div>
		</td>
	</tr>
</table>

<!--AUTH MANAGE HOLDER-->
<div id="auth_manage_holder" class="overlay">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="header_holder">
				<span class="title">
					权限管理
					<b class="auth_obj"></b>
				</span>
				<a class="btn_close" onclick="auth_manage('close');"></a>				
			</td>
		</tr>
		<tr>
			<td id="auth_manage_panel">
				
				<div class="assign_holder">
<div class="tab_container">
	<ul class="tab_holder move">
		<li><a id="subtab_1" class="subtab">部门</a></li>
		<li><a id="subtab_2" class="subtab">用户</a></li>		
	</ul>
</div>
<table id="subtab_panel_1" class="subtab_panel assign_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="left_col" width="50%" valign="top">
			<span>
				<label for="all_wad">
					<input type="checkbox" id="all_wad" class="check_all">
					可选部门
				</label>
			</span>
			<div class="clear"></div>
			<div class="list_holder">
				<ul id="wad_list"></ul>
			</div>
		</td>
		<td class="mid_col">
			<div class="btn_holder">
				<button class="move_item add"></button>
				<button class="move_item remove"></button>
			</div>
		</td>
		<td class="right_col" width="50%" valign="top">
			<span>								
				<label for="all_wsd">
					<input type="checkbox" id="all_wsd" class="check_all">
					白名单部门
				</label>
			</span>
			<div class="clear"></div>
			<div class="list_holder">
				<ul id="wsd_list"></ul>
			</div>
		</td>
	</tr>
</table>
<table id="subtab_panel_2" class="subtab_panel assign_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="left_col" width="50%" valign="top">
			<span>
				<label for="all_wau">
					<input type="checkbox" id="all_wau" class="check_all">
					可选用户
				</label>
				<input type="text" id="wau_filter" class="filter_box" placeholder="输入姓名筛选">
			</span>
			<div class="clear"></div>
			<div class="list_holder">
				<ul id="wau_list"></ul>
			</div>
		</td>
		<td class="mid_col">
			<div class="btn_holder">
				<button class="move_item add"></button>
				<button class="move_item remove"></button>
			</div>
		</td>
		<td class="right_col" width="50%" valign="top">
			<span>								
				<label for="all_wsu">
					<input type="checkbox" id="all_wsu" class="check_all">
					白名单用户
				</label>
			</span>
			<div class="clear"></div>
			<div class="list_holder">
				<ul id="wsu_list"></ul>
			</div>
		</td>
	</tr>
</table>
				</div>
				
				<div class="assign_holder" style="margin-top:10px;">
<div class="tab_container">
	<ul class="tab_holder move">
		<li><a id="subtab_3" class="subtab">部门</a></li>
		<li><a id="subtab_4" class="subtab">用户</a></li>
	</ul>
</div>
<table id="subtab_panel_3" class="subtab_panel assign_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="left_col" width="50%" valign="top">
			<span>
				<label for="all_bad">
					<input type="checkbox" id="all_bad" class="check_all">
					可选部门
				</label>
			</span>
			<div class="clear"></div>
			<div class="list_holder">
				<ul id="bad_list"></ul>
			</div>
		</td>
		<td class="mid_col">
			<div class="btn_holder">
				<button class="move_item add"></button>
				<button class="move_item remove"></button>
			</div>
		</td>
		<td class="right_col" width="50%" valign="top">
			<span>								
				<label for="all_bsd">
					<input type="checkbox" id="all_bsd" class="check_all">
					黑名单部门
				</label>
			</span>
			<div class="clear"></div>
			<div class="list_holder">
				<ul id="bsd_list"></ul>
			</div>
		</td>
	</tr>
</table>
<table id="subtab_panel_4" class="subtab_panel assign_panel" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="left_col" width="50%" valign="top">
			<span>
				<label for="all_bau">
					<input type="checkbox" id="all_bau" class="check_all">
					可选用户
				</label>
				<input type="text" id="bau_filter" class="filter_box" placeholder="输入姓名筛选">
			</span>
			<div class="clear"></div>
			<div class="list_holder">
				<ul id="bau_list"></ul>
			</div>
		</td>
		<td class="mid_col">
			<div class="btn_holder">
				<button class="move_item add"></button>
				<button class="move_item remove"></button>
			</div>
		</td>
		<td class="right_col" width="50%" valign="top">
			<span>								
				<label for="all_bsu">
					<input type="checkbox" id="all_bsu" class="check_all">
					黑名单用户
				</label>
			</span>
			<div class="clear"></div>
			<div class="list_holder">
				<ul id="bsu_list"></ul>
			</div>
		</td>
	</tr>
</table>
				</div>
				
			</td>
		</tr>
		<tr>
			<td class="ctrl_btn_holder">
				<button class="ctrl_btn sub active" onclick="auth_manage('save');">
					保存
				</button>
			</td>
		</tr>
	</table>
</div>

<script>
var post_url = 'media_manage.php';
	post_id = '<?php print $_COOKIE['media_id'];?>',
	id_prefix = 'm_';

$(function() {
	if(post_id>0){
		post_last++;
		expand_node(post_id);
	}
	else{//first load or home page
		curr_id = $('.node_tree li:eq(0)').attr('id').replace(id_prefix,'');
		if(curr_id==0){			
			add_node(curr_id);
		}
		else{
			edit_node(curr_id);
		}
	}
});

function edit_node(m_id){
	set_curr(m_id);
	if($('#'+id_prefix+m_id+' .toogle:eq(0)').hasClass('shrink'))toogle_node(m_id);
	$('#content_panel').html('<div class="loading cover"></div>').load('media_control.php?m_id='+m_id);	
}

function add_node(m_id){
	set_curr(m_id);
	document.cookie = 'media_tab=;';
	$('#content_panel').html('<div class="loading cover"></div>').load('media_control.php?parent_id='+m_id);
}

function delete_node(m_id){
	var media_type = '';
	switch($('#'+id_prefix+m_id).attr('class')){
		case 'folder':media_type = '文件夹';break;
		case 'file':media_type = '文件';break;
	}	
	
	var str_del_child = '';
	if($('#'+id_prefix+m_id+' .toogle').length>0){
		str_del_child = '及其子项目';
	}
	
	if(confirm('确定删除'+media_type+'['+$('#'+id_prefix+m_id+' .node_link:eq(0)').text().trim()+']'+str_del_child+'?')){
		show_alert('正在删除'+media_type+str_del_child+'，请稍候 ...','load');
		
		var params = 'action=delete&m_id='+m_id+'&type='+$('#'+id_prefix+m_id).attr('class');
		$.ajax({
			type: 'post',
			url: post_url,
			dataType: 'json',
			data: params,
			success: function(result){
				if(result.success==1){
					if(curr_id==m_id){
						document.cookie = 'media_id='+result.parent_id;
					}
					window.location.reload();
				}	
				else{
					show_alert(result.error);				
				}	
			}
		});
	}	
}
</script>

<?php
include("media_content.php");
include("public_content.php");
include("footer.php");
?>